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

if (!(isValidPostVar('courseID') && isValidPostVar('studentID'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $courseID = $_POST['courseID'];
    $studentID = $_POST['studentID'];
    if (insertEnrollment($studentID, $courseID, $conn)) {
        echo ENROLLMENT_ADDED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


