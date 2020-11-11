<?php

/* Imports */
require_once 'error.php';
require_once 'fineCalculator.php';
require_once 'statusConstants.php';

function renewBook($renewerID, $bookBarcode, $conn, $debug = false)
{
    try {
        return
            $conn->autocommit(false) &&
            /* calculating overdue fee if book is checked out */
            addOverdueFines($bookBarcode, $conn, $debug) &&
            /* Renew the book - change the new due date and increment renewal */
            renewBookQuery($renewerID, $bookBarcode, $conn, $debug) &&
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

function renewBookQuery($renewerID, $bookBarcode, $conn, $debug = false): bool
{
    $query = "
                UPDATE `transactions` 
                    INNER JOIN bookItem ON bookItem.bookBarcode=transactions.bookBarcode
                    INNER JOIN books ON books.bookISBN=bookItem.bookISBN
                SET 
                    renewedTime = renewedTime +1, 
                    dueDate=CURDATE() 
                WHERE 
                    transactions.borrowedBy =? 
                    AND transactions.bookBarcode =? 
                    AND transactions.returnDate IS NULL 
                    AND transactions.renewedTime<? 
                    AND books.express=0 
                    AND books.holds=0 
                    AND bookItem.reservedFor IS NULL 
                    AND bookItem.status=?
             ";
    $stmt = $conn->prepare($query);
    $maxRenewalTime = MAXIMUM_RENEWAL_TIME;
    $checkedOut = BOOK_CHECKED_OUT;
    $success = $stmt->bind_param(
        "iiii",
        $renewerID,
        $bookBarcode,
        $maxRenewalTime,
        $checkedOut
    );
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, "renewBookQuery");
    }

    return $success && $numRows == 1;
}

