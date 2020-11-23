<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/manageEnrollmentRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

/* Check that parameter are set */
if (!(isValidPostVar('enrollmentNumber'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $enrollmentNumber = $_POST['enrollmentNumber'];
    $debug = DEBUG;
    if (deleteEnrollment($enrollmentNumber, $conn, $debug)) {
        echo ENROLLMENT_DELETED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}


$conn = null;

