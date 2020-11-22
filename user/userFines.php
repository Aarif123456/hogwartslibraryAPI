<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userFinesRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $userID = $_SESSION['userID'];
    $listType = $_REQUEST['listType'] ?? '';
    $result = getUserFines($userID, $listType, $conn);
    if (empty($result)) {
        exit(INVALID_LIST);
    }
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;


