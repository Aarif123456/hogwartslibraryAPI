<?php
/* Abdullah Arif */

/* Calculate fees on user account */
/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function addOverdueFines($bookBarcode, $conn, $debug = false)
{
    $stmt = $conn->prepare(
        "UPDATE bookItem, transactions
            SET fine = fine + ABS(DATEDIFF(CURDATE(),dueDate))*?
            WHERE returnDate IS NULL 
            AND transactions.bookBarcode=? 
            AND transactions.bookBarcode=bookItem.bookBarcode
            AND CURDATE()>transactions.dueDate 
            AND bookItem.status=?"
    );
    $finePerBookOverdue = FINE_PER_DAY_OVERDUE;
    $bookCheckedOut = BOOK_CHECKED_OUT;
    $success = $stmt->bind_param("dii", $finePerBookOverdue, $bookBarcode, $bookCheckedOut) && $stmt->execute();
    if ($debug) {
        echo "FUNCTION addOverdueFines: row affected " . $conn->affected_rows;
        echo "FUNCTION addOverdueFines: successful = " . $success;
    }

    return $success;
}

