<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userCheckoutRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser()) {
    $userID = $_SESSION['userID'];
    $result = getUserCheckedOutBooks($userID, $conn);
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn->close();

