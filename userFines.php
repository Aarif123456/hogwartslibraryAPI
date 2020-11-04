<?php

/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/userFinesRepo.php';

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser()) {
    $userID = $_SESSION['userID'];
    $listType = $_REQUEST['listType'] ?? "";
    $result = getUserFines($userID, $listType, $conn);
    if (empty($result)) {
        exit(INVALID_LIST);
    }
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn->close();


