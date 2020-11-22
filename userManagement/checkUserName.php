<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/usernameRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

/* Code starts */
if (isValidPostVar('username')) {
    $result = queryUsername($_POST['username'], $conn);
    if (count($result) == 0) {
        echo USERNAME_NOT_IN_TABLE;
    } else {
        echo USERNAME_EXISTS;
    }
} else {
    echo MISSING_PARAMETERS;
}

$conn = null;
