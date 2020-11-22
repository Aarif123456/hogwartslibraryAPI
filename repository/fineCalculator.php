<?php
/* Abdullah Arif */

/* Calculate fees on user account */
/* Imports */
require_once __DIR__ .'/error.php';
require_once __DIR__ .'/statusConstants.php';

function addOverdueFines($bookBarcode, $conn, $debug = false)
{
    $stmt = $conn->prepare(
        'UPDATE bookItem, transactions
            SET fine = fine + ABS(DATEDIFF(CURDATE(),dueDate)) * :finesPerBookOverdue
            WHERE returnDate IS NULL 
            AND transactions.bookBarcode = :bookBarcode
            AND transactions.bookBarcode = bookItem.bookBarcode
            AND CURDATE()>transactions.dueDate 
            AND bookItem.status = :checkedOut'
    );
    $success = $stmt->bindValue(':finesPerBookOverdue', strval(FINE_PER_DAY_OVERDUE), PDO::PARAM_STR) &&
               $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT) &&
               $stmt->bindValue(':checkedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT);

    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'addOverdueFines');
    }

    return $success;
}

