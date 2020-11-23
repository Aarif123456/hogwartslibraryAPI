<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/usernameRepo.php';

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
