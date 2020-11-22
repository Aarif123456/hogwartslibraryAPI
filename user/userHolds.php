<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userHoldRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $userID = $_SESSION['userID'];
    $result = getUserHoldsResults($userID, $conn);
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;


