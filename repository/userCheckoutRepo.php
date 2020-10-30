<?php
/* Code for queries that will be used in the menu's about the user */
/* Imports */
require_once 'error.php';

function getUserCheckedOutBooks($userID, $conn){
    // get query
    $transactionStmt = $conn->prepare("SELECT bookName, author,bookBarcode, dueDate, 
        renewedTime, holds FROM `transactions` NATURAL JOIN `bookItem` NATURAL JOIN
        `books` WHERE transactions.borrowedBy = ? AND transactions.returnDate IS NULL");
    $transactionStmt->bind_param("i", $userID);
    // if no query return back null
    if(empty($transactionStmt)) return null;
    // otherwise return back result from query
    return getExecutedResult($transactionStmt);
}

?>