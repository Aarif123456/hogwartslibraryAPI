<?php

/* Imports */
require_once 'error.php';

function queryDeveloperPassword($developerID, $conn)
{
    $verifyQuery = "SELECT `password` FROM `userAccount` INNER JOIN `headmasters` ON `userID` = `headmasterID` WHERE userID = ?";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->bind_param("i", $developerID);

    return getExecutedResult($verifyStmt);
}

