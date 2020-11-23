<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/fineCalculator.php';
require_once __DIR__ . '/statusConstants.php';

function renewBook($renewerID, $bookBarcode, $conn, $debug = false)
{
    try {
        return
            $conn->beginTransaction() &&
            /* calculating overdue fee if book is checked out */
            addOverdueFines($bookBarcode, $conn, $debug) &&
            /* Renew the book - change the new due date and increment renewal */
            renewBookQuery($renewerID, $bookBarcode, $conn, $debug) &&
            $conn->commit();
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
    $query = '
                UPDATE `transactions` 
                    INNER JOIN bookItem ON bookItem.bookBarcode = transactions.bookBarcode
                    INNER JOIN books ON books.bookISBN = bookItem.bookISBN
                SET 
                    renewedTime = renewedTime + 1, 
                    dueDate = CURDATE() 
                WHERE 
                    transactions.borrowedBy = :id 
                    AND transactions.bookBarcode = :bookBarcode
                    AND transactions.returnDate IS NULL 
                    AND transactions.renewedTime < :maxRenewalTime
                    AND books.express = 0 
                    AND books.holds = 0 
                    AND bookItem.reservedFor IS NULL 
                    AND bookItem.status = :checkedOut
             ';
    $stmt = $conn->prepare($query);
    $success = $stmt->bindValue(':maxRenewalTime', MAXIMUM_RENEWAL_TIME, PDO::PARAM_INT) &&
               $stmt->bindValue(':checkedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT) &&
               $stmt->bindValue(':id', $renewerID, PDO::PARAM_INT) &&
               $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'renewBookQuery');
    }

    return $success && $numRows === 1;
}

