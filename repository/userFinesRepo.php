<?php
/* Code for queries that will be used for the fines on the user account */

/* Imports */
require_once __DIR__ . '/error.php';

function getUserFines($userID, $listType, $conn)
{
    // get query
    $query = getFineQuery($listType);
    // if no query return back null
    if (empty($query)) {
        exit(INVALID_LIST);
    }
    $fineStmt = $conn->prepare($query);
    $fineStmt->bindValue(':id', $userID, PDO::PARAM_INT);

    // otherwise return back result from query
    return getExecutedResult($fineStmt);
}

function getFineQuery($listType)
{
    switch ($listType) {
        case 'getTransactionWithFines':
            return 'SELECT transactionID,bookName,author,bookBarcode,fine,returnDate,price FROM `transactions` NATURAL JOIN `bookItem` NATURAL JOIN `books` WHERE borrowedBy=:id AND fine >0 ORDER BY IF(returnDate is NULL, 0, 1), returnDate DESC';
        case 'getOutstandingFineOnAccount':
            /* get total fine */
            return 'SELECT fines FROM `members` WHERE memberID = :id';
        default:
            return '';
    }
}

function payFine($pay, $userID, $conn, $debug = false)
{
    $fineStmt = $conn->prepare(
        'UPDATE members SET fines = IF(fines<=:payCompare, 0, fines - :paySubtract) WHERE `memberID` = :id'
    );
    $fineStmt->bindValue(':payCompare', $pay, PDO::PARAM_STR);
    $fineStmt->bindValue(':paySubtract', $pay, PDO::PARAM_STR);
    $fineStmt->bindValue(':id', $userID, PDO::PARAM_INT);

    // otherwise return back result from query

    return $pay > 0 && safeWriteQueries($fineStmt, $conn, $debug);
}
