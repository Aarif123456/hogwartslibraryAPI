<?php
/* Code for queries that will be used in the menu's about the user */
/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function getUserHoldsResults($userID, $conn){
    // get query
    $holdStmt = $conn->prepare("SELECT * FROM `holds` NATURAL JOIN `books` WHERE holderID = ? AND (holds.status=? OR holds.status=?)");
    $holdStmt->bind_param("iii", $userID, HOLD_ACTIVE, HOLD_IN_QUEUE);
    // if no query return back null
    if(empty($holdStmt)) return null;
    // otherwise return back result from query
    return getExecutedResult($holdStmt);
}
?>