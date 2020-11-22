<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/manageCourseRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('courseID'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $courseID = $_POST['courseID'];
    if (deleteCourse($courseID, $conn)) {
        echo COURSE_DELETED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


