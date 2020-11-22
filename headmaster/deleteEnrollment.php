<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/manageEnrollmentRepo.php';

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
    if (deleteEnrollment($enrollmentNumber, $conn)) {
        echo ENROLLMENT_DELETED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}


$conn = null;

