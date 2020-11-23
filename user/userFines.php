<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/userFinesRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $userID = getUserID($conn);
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


