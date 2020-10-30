<?php
/* Code for queries that will be used in the menu's about the user */
/* Imports */
require_once 'error.php';

function getUserHoldsResults($userID, $conn){
    // get query
    $holdStmt = $conn->prepare("SELECT * FROM `holds` NATURAL JOIN `books` WHERE holderID = ? AND (holds.status = 11 OR holds.status=14)");
    $holdStmt->bind_param("i", $userID);
    // if no query return back null
    if(empty($holdStmt)) return null;
    // otherwise return back result from query
    return getExecutedResult($holdStmt);
}
?>