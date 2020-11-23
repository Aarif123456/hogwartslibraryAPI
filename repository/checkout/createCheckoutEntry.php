<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


/* Check out book if the following condition are true
    1. One of the following 
        a. book is available
        b. on hold for user
    2. One of the following 
        a. Not reserved for a class
        b. user in class the book is reserved For 
*/
function createCheckOutTransaction($bookBarcode, $borrowedBy, $librarianID, $conn, $debug = false)
{
    $query = 'INSERT INTO transactions (bookBarcode, borrowedBy, issuerID, borrowedDate, dueDate) 
        SELECT bookBarcode, 
        :borrowedBy AS borrowedBy, 
        :librarianID AS issuerID,
        CURDATE() as borrowedDate,
        (
            IF(bookBarcode IN (
                SELECT bookBarcode FROM books NATURAL JOIN bookItem WHERE
                    bookBarcode = :bookBarcodeExpress AND express = 1
            ), DATE_ADD(CURDATE(), INTERVAL :expressLending DAY), DATE_ADD(CURDATE(), INTERVAL :normalLending DAY))
        ) as dueDate
        FROM bookItem WHERE
            bookBarcode = :bookBarcode AND  
            ( bookItem.reservedFor IS NULL
                OR bookItem.reservedFor IN (
                    SELECT courseID AS reservedFor
                    FROM courseEnrolled NATURAL JOIN courses WHERE
                        courseEnrolled.studentID = :studentID AND TermOffered = :currentTerm
                    )
            ) AND 
            ( bookItem.status = :bookAvailable
                OR  ( bookItem.status = :bookOnHold AND 1 =
                        ( SELECT COUNT(*) FROM holds WHERE 
                            holderID = :holderID
                            AND holds.status = :readyForPickup
                            AND holds.reservedCopy = :reservedCopy
                        )
                    ) 
            )';
    $stmt = $conn->prepare($query);
    // For insert
    $stmt->bindValue(':borrowedBy', $borrowedBy, PDO::PARAM_INT);
    // For insert
    $stmt->bindValue(':librarianID', $librarianID, PDO::PARAM_INT);
    // Used to check if book is express
    $stmt->bindValue(':bookBarcodeExpress', $bookBarcode, PDO::PARAM_INT);
    // Change amount book is lending for depending on if it an express book
    $stmt->bindValue(':expressLending', EXPRESS_LENDING_DAYS, PDO::PARAM_INT);
    $stmt->bindValue(':normalLending', NORMAL_LENDING_DAYS, PDO::PARAM_INT);
    // Select the correct bookItem by primary key
    $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT);
    // studentId to check if student is enrolled in class
    $stmt->bindValue(':studentID', $borrowedBy, PDO::PARAM_INT);
    // Ensure student is enrolled for current term
    $stmt->bindValue(':currentTerm', CURRENT_TERM, PDO::PARAM_INT);
    $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT);
    $stmt->bindValue(':bookOnHold', BOOK_ON_HOLD, PDO::PARAM_INT);
    // In hold table make sure the holder is correct
    $stmt->bindValue(':holderID', $borrowedBy, PDO::PARAM_INT);
    $stmt->bindValue(':readyForPickup', HOLD_IN_QUEUE, PDO::PARAM_INT);
    $stmt->bindValue(':reservedCopy', $bookBarcode, PDO::PARAM_INT);
    $tranId = safeInsertQueries($stmt, $conn, $debug);
    if (empty($tranId)) {
        exit(CHECKOUT_FAILED);
    }

    return $tranId;
}