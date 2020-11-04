<?php
/* Code for queries that will be used in the menu's about the user */

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function getUserCheckedOutBooks($userID, $conn)
{
    // get query
    $transactionStmt = $conn->prepare(
        "SELECT bookName, author,bookBarcode, dueDate, 
        renewedTime, holds FROM `transactions` NATURAL JOIN `bookItem` NATURAL JOIN
        `books` WHERE transactions.borrowedBy = ? AND transactions.returnDate IS NULL"
    );
    $transactionStmt->bind_param("i", $userID);
    // if no query return back null
    if (empty($transactionStmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($transactionStmt);
}

/* User is eligible for check out if they meet the following criteria 
    1. They have an active account
    2. Are one of the following
        a. a student with less than 10 books checked out 
        b. professor with less than 50 books checked out 
        c. headmaster
*/
function checkUserEligibleForCheckout($userID, $conn)
{
    $memberActive = "SELECT memberID FROM members WHERE status=" . ACCOUNT_ACTIVE;
    $selectStart = " AND memberID IN (";
    $combiner = " UNION ";
    $studentInLimit = "SELECT memberID FROM members INNER JOIN students ON memberID=studentID WHERE memberID=? AND numBooks < " . STUDENT_BOOK_LIMIT;
    $profInLimit = "SELECT memberID FROM members INNER JOIN professor ON memberID=professorID WHERE memberID=? AND numBooks < " . PROFESSOR_BOOK_LIMIT;
    $headmaster = "SELECT memberID FROM members INNER JOIN headmasters ON memberID=headmasterID WHERE memberID=?";
    $selectEnd = ")";
    $query = $memberActive . $selectStart .
             $studentInLimit . $combiner .
             $profInLimit . $combiner .
             $headmaster .
             $selectEnd;

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iii",
        $userID,     // Condition 2a
        $userID,     // Condition 2b
        $userID      // Condition 2c
    );

    return getExecutedResult($stmt);
}

function checkOutBook($bookBarcode, $borrowedBy, $librarianID, $conn, $debug = false)
{
    try {
        $conn->autocommit(false);
        /* create checkout transaction */
        $id = createCheckOutTransaction($bookBarcode, $borrowedBy, $librarianID, $conn);
        /* Update information related to the transaction */
        updateCheckOutInfo($id, $conn);
        /* Update hold table if we need */
        updateHoldTable($id, $conn);
        $conn->autocommit(true);

        return $id;
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        $conn->rollback();
        if ($debug) {
            debugPrint($e, $conn);
        }
        exit(WRITE_QUERY_FAILED);
    }

}

/* Check out book if the following condition are true
    1. One of the following 
        a. book is available
        b. on hold for user
    2. One of the following 
        a. Not reserved for a class
        b. user in class the book is reserved For 
*/
function createCheckOutTransaction($bookBarcode, $borrowedBy, $librarianID, $conn)
{
    $query = "INSERT INTO transactions (bookBarcode, borrowedBy, issuerID, borrowedDate, dueDate) 
        SELECT bookBarcode, 
        ? AS borrowedBy, 
        ? AS issuerID,
        CURDATE() as borrowedDate,
        (
            IF(bookBarcode IN (
                SELECT bookBarcode FROM books NATURAL JOIN bookItem WHERE
                    bookBarcode = ? AND express = 1
            ), DATE_ADD(CURDATE(), INTERVAL ? DAY), DATE_ADD(CURDATE(), INTERVAL ? DAY))
        ) as dueDate
        FROM bookItem WHERE
            bookBarcode = ? AND  
            ( bookItem.reservedFor IS NULL
                OR bookItem.reservedFor IN (
                    SELECT courseID AS reservedFor
                    FROM courseEnrolled NATURAL JOIN courses WHERE
                        courseEnrolled.studentID = ? AND TermOffered=?
                    )
            ) AND 
            ( bookItem.status = ?
                OR  ( bookItem.status = ? AND 1=
                        ( SELECT COUNT(*) FROM holds WHERE 
                            holderID = ?
                            AND holds.status = ?
                            AND holds.reservedCopy = ?
                        )
                    ) 
            )";
    $expressLending = EXPRESS_LENDING_DAYS;
    $normalLending = NORMAL_LENDING_DAYS;
    $currentTerm = CURRENT_TERM;
    $bookAvailable = BOOK_AVAILABLE;
    $bookOnHold = BOOK_ON_HOLD;
    $readyForPickup = HOLD_IN_QUEUE;
    $transactionStmt = $conn->prepare($query);
    $transactionStmt->bind_param(
        "iiiiiiiiiiiii",
        $borrowedBy,              // For insert
        $librarianID,             // For insert
        $bookBarcode,             // Used to check if book is express
        $expressLending,          // lending day for express books
        $normalLending,           // lending day for normal books
        $bookBarcode,             // Select the correct bookItem by primary key
        $borrowedBy,              // studentId to check if student is enrolled in class
        $currentTerm,             // Make student is enrolled for current term
        $bookAvailable,           // Check if book is available
        $bookOnHold,              // If on hold check hold table
        $borrowedBy,              // In hold table make sure the holder is correct
        $readyForPickup,          // Ensure the book is waiting for pick up
        $bookBarcode              // Make sure we select the correct book in holds table
    );
    $transactionStmt->execute();
    $tranId = $conn->insert_id;
    if (empty($tranId)) {
        exit(CHECKOUT_FAILED);
    }

    return $tranId;
}

function updateCheckOutInfo($id, $conn)
{
    $updateQuery = "UPDATE members, bookItem, transactions
        SET bookItem.status = ?,
            members.numBooks = members.numBooks + 1
            WHERE transactions.transactionID=? AND
                bookItem.bookBarcode=transactions.bookBarcode AND 
                members.memberID=transactions.borrowedBy
                ";
    $checkOutStmt = $conn->prepare($updateQuery);
    $bookCheckedOut = BOOK_CHECKED_OUT;
    $checkOutStmt->bind_param("ii", $bookCheckedOut, $id);
    $checkOutStmt->execute();
    if ($conn->affected_rows != 2) {
        exit(CHECKOUT_UPDATE_FAILED);
    }
}

function updateHoldTable($id, $conn)
{
    $updateQuery = "UPDATE holds INNER JOIN transactions ON 
                        reservedCopy = bookBarcode AND 
                        holderID = borrowedBy
                        NATURAL JOIN books
                        SET holds.status = ?,
                        books.holds= books.holds-1
                        WHERE 
                            holds.status=? AND
                            transactionID=?";
    $checkOutStmt = $conn->prepare($updateQuery);
    $holdCompleted = HOLD_COMPLETED;
    $readyForPickup = HOLD_IN_QUEUE;
    $checkOutStmt->bind_param("iii", $holdCompleted, $readyForPickup, $id);
    $checkOutStmt->execute();
}

