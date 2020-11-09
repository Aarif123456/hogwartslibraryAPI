<?php

/* Imports */
require_once 'error.php';
require_once 'fineCalculator.php';
require_once 'statusConstants.php';

/* Return book means we need add the return date to the transaction as well as the librarian who returned the book. 
*   Then we need to add the fine on the transaction to the user account
*   We also need to mark the book as available and subtract the number pf books on their account 
*/

function returnBook($todayDate, $librarianID, $bookBarcode, $conn, $debug = false)
{
    try {
        return
            $conn->autocommit(false) &&
            /* calculating overdue fee if book is checked out */
            addOverdueFines($bookBarcode, $conn, $debug) &&
            /* Return book, add fine to user account, change book status */
            updateReturnInfo($todayDate, $librarianID, $bookBarcode, $conn, $debug) &&
            /* If their active holds on the book update the status to ON_HOLD and update reserved copy for earliest active hold */
            reserveCopyForHolds($bookBarcode, $conn, $debug) &&
            $conn->autocommit(true);
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        $conn->rollback();
        if ($debug) {
            debugPrint($e, $conn);
        }
        exit(WRITE_QUERY_FAILED);
    }
}


function updateReturnInfo($todayDate, $librarianID, $bookBarcode, $conn, $debug = false)
{
    $stmt = $conn->prepare(
        "UPDATE members, bookItem, transactions
                            SET bookItem.status = ?,
                                transactions.returnerID=?,
                                transactions.returnDate=DATE(?),
                                members.fines = members.fines + transactions.fine,
                                members.numBooks = members.numBooks - 1, 
                                members.status = IF (
                                                        (
                                                            (
                                                                members.fines >=? 
                                                                AND members.memberID IN 
                                                                (
                                                                    SELECT studentID AS memberID FROM students
                                                                )
                                                            ) OR 
                                                            (
                                                                members.fines >=? 
                                                                AND members.memberID IN 
                                                                (
                                                                    SELECT professorID AS memberID FROM professor
                                                                )
                                                            )
                                                        ), 
                                                        ?, 
                                                        members.status 
                                                    )
                                WHERE transactions.returnDate IS NULL 
                                    AND transactions.bookBarcode =?
                                    AND bookItem.bookBarcode=transactions.bookBarcode
                                    AND members.memberID=transactions.borrowedBy 
                                    AND bookItem.status=?
                "
    );

    $bookAvailable = BOOK_AVAILABLE;
    $bookCheckedOut = BOOK_CHECKED_OUT;
    $blacklisted = ACCOUNT_BLACKLISTED;
    $studentLimit = STUDENT_BOOK_LIMIT;
    $professorLimit = PROFESSOR_BOOK_LIMIT;

    $success = $stmt->bind_param(
            "iisiiiii",
            $bookAvailable,
            $librarianID,
            $todayDate,
            $studentLimit,
            $professorLimit,
            $blacklisted,
            $bookBarcode,
            $bookCheckedOut
        ) && $stmt->execute();
    if ($debug) {
        echo "FUNCTION updateReturnInfo: row affected " . $conn->affected_rows;
        echo "FUNCTION updateReturnInfo: successful = " . $success;
    }

    return $conn->affected_rows == 3 && $success;
}

/* When the book get returned we check the holds table to see if the book has any holds, if it does we reserve this copy to 
*   the user first in line.
* We also need to factor in reservation, so if the book has been reserved we will look for user's in the course before reserving 
*/

function reserveCopyForHolds($bookBarcode, $conn, $debug = false)
{
    $query =
        "
                UPDATE
                (
                    SELECT *
                        FROM holds as h WHERE
                            h.status = ?
                            AND h.bookISBN IN 
                            (
                                SELECT bookISBN
                                    FROM bookItem AS BK WHERE 
                                        bookBarcode = ?
                                        AND 
                                        (
                                            BK.reservedFor IS NULL
                                            OR BK.reservedFor IN 
                                            (
                                                SELECT courseID AS reservedFor
                                                    FROM courseEnrolled WHERE
                                                        studentID = h.holderID
                                            )
                                        )
                            )
                        ORDER BY timeStamp
                        LIMIT 1
                ) AS filteredHold 
                    INNER JOIN bookItem ON bookItem.bookISBN=filteredHold.bookISBN
                    INNER JOIN holds ON filteredHold.holdID=holds.holdID
                    INNER JOIN books ON filteredHold.bookISBN=book.bookISBN
                    SET
                            holds.status = ?,
                            holds.reservedCopy=?,
                            holds.holdExpiryDate= DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
                            bookItem.status = ?,
                            books.hold = books.hold -1
                    WHERE
                    bookItem.bookBarcode=?
                    AND bookItem.status =?
            ";
    $stmt = $conn->prepare($query);
    $inQueue = HOLD_IN_QUEUE;
    $activeHold = HOLD_ACTIVE;
    $onHold = BOOK_ON_HOLD;
    $bookAvailable = BOOK_AVAILABLE;
    $success = $stmt->bind_param(
            "iiiiiii",
            $inQueue,                   // only target holds in waiting
            $bookBarcode,               // primary key in bookItem used to handle reservation
            $activeHold,                // Set hold status to active now that we have a copy
            $bookBarcode,               // reserve the copy for the hold
            $onHold,                    // change book status to ON_HOLD
            $bookBarcode,               // make sure we only get one element from bookItem
            $bookAvailable              // Make sure book is available
        ) && $stmt->execute();
    if ($debug) {
        echo "FUNCTION reserveCopyForHolds: row affected " . $conn->affected_rows;
        echo "FUNCTION reserveCopyForHolds: successful = " . $success;
    }

    return $success;
}

/* If lost book is found then return a portion of the money and mark book as available */
function refundLostBook($bookBarcode, $conn, $debug = false)
{
    $query = "UPDATE bookItem, members, transactions
            SET members.fines = members.fines-bookItem.price*?,
            bookItem.status = ? 
            WHERE bookItem.status= ? 
            AND bookItem.bookBarcode=? 
            AND transactions.bookBarcode=bookItem.bookBarcode
            AND transactions.borrowedBy=members.memberID";
    $percentRefunded = REFUND_FOR_FOUND_BOOK;
    $bookAvailable = BOOK_AVAILABLE;
    $bookLost = BOOK_LOST;
    $stmt = $conn->prepare($query);
    $success = $stmt->bind_param("diii", $percentRefunded, $bookAvailable, $bookLost, $bookBarcode) && $stmt->execute();
    if ($debug) {
        echo "FUNCTION refundLostBook: row affected " . $conn->affected_rows;
        echo "FUNCTION refundLostBook: successful = " . $success;
    }

    return $conn->affected_rows == 2 && $success;
}