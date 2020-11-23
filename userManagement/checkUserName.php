<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

/* Code starts */
if (isValidPostVar('username')) {
    $auth = getAuth($conn);
    $email = $_POST['username'];
    if ($auth->isEmailTaken($email)) {
        echo USERNAME_EXISTS;
    } else {
        echo USERNAME_NOT_IN_TABLE;
    }
} else {
    echo MISSING_PARAMETERS;
}

$conn = null;
