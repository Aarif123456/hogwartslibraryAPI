<?php
/* Code for queries that will be used for the fines on the user account */
/* Imports */
require_once 'error.php';

function getUserFines($userID, $listType, $conn){
    // get query
    $query = getFineQuery($listType);
    // if no query return back null
    if(empty($query)) return null;
    $fineStmt = $conn->prepare($query);
    $fineStmt->bind_param("i", $userID);
    // otherwise return back result from query
    return getExecutedResult($fineStmt);
}

function getFineQuery($listType){
    switch ($listType) {
        case "getTransactionWithFines":
            return "SELECT transactionID,bookName,author,bookBarcode,fine,returnDate,price FROM `transactions` NATURAL JOIN `bookItem` NATURAL JOIN `books` WHERE borrowedBy=? AND fine >0 ORDER BY CASE WHEN returnDate is NULL THEN 0 ELSE 1 END, returnDate DESC";
        case "getOutstandingFineOnAccount":
            /* get total fine */
            return "SELECT fines FROM `members` WHERE memberID = ?"; 
        default:
            return "";
    }
}

?>