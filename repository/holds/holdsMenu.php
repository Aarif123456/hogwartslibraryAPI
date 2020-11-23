<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


function getUserHoldsResults($userID, $conn)
{
    // get query
    $query = 'SELECT * FROM `holds` NATURAL JOIN `books` WHERE 
                holderID = :id AND (holds.status = :activeHold
                OR holds.status = :inQueue)';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':id', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':activeHold', HOLD_ACTIVE, PDO::PARAM_INT);
    $stmt->bindValue(':inQueue', HOLD_IN_QUEUE, PDO::PARAM_INT);
    // if no query return back null
    if (empty($stmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($stmt);
}