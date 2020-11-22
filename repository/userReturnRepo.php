<?php

/* Imports */
require_once __DIR__ .'/error.php';
require_once __DIR__ .'/fineCalculator.php';
require_once __DIR__ .'/statusConstants.php';
require_once __DIR__ .'/return/reserveHolds.php';
require_once __DIR__ .'/return/updateReturnTables.php';
require_once __DIR__ .'/return/refundLostBook.php';

/* Return book means we need add the return date to the transaction as well as the librarian who returned the book. 
*   Then we need to add the fine on the transaction to the user account
*   We also need to mark the book as available and subtract the number of books on their account 
*/

function returnBook($parameter, $conn, $debug = false)
{
    $bookBarcode = $parameter->bookBarcode;
    try {
        return
            $conn->beginTransaction() &&
            /* calculating overdue fee if book is checked out */
            addOverdueFines($bookBarcode, $conn, $debug) &&
            /* Return book, add fine to user account, change book status */
            updateReturnInfo($parameter, $conn, $debug) &&
            /* If their active holds on the book update the status to ON_HOLD and update reserved copy for earliest active hold */
            reserveCopyForHolds($parameter, $conn, $debug) &&
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

