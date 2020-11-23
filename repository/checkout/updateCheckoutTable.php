<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


function updateCheckOutInfo($id, $conn, $debug = false)
{
    $updateQuery = 'UPDATE members, bookItem, transactions
        SET bookItem.status = :bookCheckedOut,
            members.numBooks = members.numBooks + 1
            WHERE transactions.transactionID = :id AND
                bookItem.bookBarcode = transactions.bookBarcode AND 
                members.memberID = transactions.borrowedBy
                ';
    $checkOutStmt = $conn->prepare($updateQuery);
    $checkOutStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkOutStmt->bindValue(':bookCheckedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT);

    $numRows = safeUpdateQueries($checkOutStmt, $conn, $debug);
    if ($numRows != 2) {
        exit(CHECKOUT_UPDATE_FAILED);
    }
}
