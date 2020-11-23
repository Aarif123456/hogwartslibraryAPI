<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


function updateHoldTable($id, $conn, $debug = false)
{
    $updateQuery = '
                    UPDATE holds 
                        INNER JOIN transactions ON reservedCopy = bookBarcode AND holderID = borrowedBy
                        NATURAL JOIN books
                    SET 
                        holds.status = :holdCompleted,
                        books.holds = books.holds-1
                    WHERE 
                        holds.status = :readyForPickup
                        AND transactionID = :id
                   ';
    $checkOutStmt = $conn->prepare($updateQuery);
    $checkOutStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkOutStmt->bindValue(':readyForPickup', HOLD_IN_QUEUE, PDO::PARAM_INT);
    $checkOutStmt->bindValue(':holdCompleted', HOLD_COMPLETED, PDO::PARAM_INT);

    safeWriteQueries($checkOutStmt, $conn, $debug);
}

