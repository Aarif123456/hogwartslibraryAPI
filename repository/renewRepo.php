<?php

/* Imports */
require_once 'error.php';
require_once 'fineCalculator.php';
require_once 'statusConstants.php';

function renewBook($renewerID, $bookBarcode, $conn, $debug = false)
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

    try {
        $success = false;

        return
            $conn->autocommit(false) &&
            /* calculating overdue fee if book is checked out */
            addOverdueFines($bookBarcode, $conn, $debug) &&
            /* Renew the book - change the new due date and increment renewal */
            ($success = $stmt->bind_param("iiii", $renewerID, $bookBarcode, $maxRenewalTime, $checkedOut) &&
                        $stmt->execute()) &&
            $conn->affected_rows == 1 &&
            $conn->autocommit(true);
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        $conn->rollback();
        if ($debug) {
            echo debugQuery($conn->affected_rows, $success, "renewBook");
            debugPrint($e, $conn);
        }
        exit(WRITE_QUERY_FAILED);
    }
}

