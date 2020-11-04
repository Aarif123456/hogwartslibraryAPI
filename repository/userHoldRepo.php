<?php
/* Code for queries that will be used in the menu's about the user */

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function getUserHoldsResults($userID, $conn)
{
    // get query
    $query = "SELECT * FROM `holds` NATURAL JOIN `books` WHERE " .
             "holderID = ? AND (holds.status=" . HOLD_ACTIVE .
             " OR holds.status=" . HOLD_IN_QUEUE . ")";
    $holdStmt = $conn->prepare($query);
    $holdStmt->bind_param("i", $userID);
    // if no query return back null
    if (empty($holdStmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($holdStmt);
}

