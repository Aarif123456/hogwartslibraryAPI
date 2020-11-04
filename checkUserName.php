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
require_once 'repository/usernameRepo.php';
/* Connect to database */
$conn = getConnection();

/* Code starts */
if (isValidPostVar('username')) {
    $result = queryUsername($_POST['username'], $conn);
    if ($result->num_rows == 0) {
        echo USERNAME_NOT_IN_TABLE;
    } else {
        echo USERNAME_EXISTS;
    }
} else {
    echo MISSING_PARAMETERS;
}

$conn->close();
