<?php
/* Code for queries that will be used in the menu's about the user */

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';
require_once __DIR__ . '/checkout/checkoutMenu.php';
require_once __DIR__ . '/checkout/checkCheckoutEligibility.php';
require_once __DIR__ . '/checkout/createCheckoutEntry.php';
require_once __DIR__ . '/checkout/updateCheckoutTable.php';
require_once __DIR__ . '/checkout/updateHoldTable.php';

function checkOutBook($bookBarcode, $borrowedBy, $librarianID, $conn, $debug = false)
{
    try {
        $conn->beginTransaction();
        /* create checkout transaction */
        $id = createCheckOutTransaction($bookBarcode, $borrowedBy, $librarianID, $conn, $debug);
        /* Update information related to the transaction */
        updateCheckOutInfo($id, $conn, $debug);
        /* Update hold table if we need */
        updateHoldTable($id, $conn, $debug);
        $conn->commit();

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

