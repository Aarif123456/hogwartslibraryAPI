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

if (!(isValidPostVar('courseName') && isValidPostVar('professorID'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster()) {
    $professorID = $_POST['professorID'];
    $courseName = $_POST['courseName'];
    $termOffered = isValidPostVar('TermOffered') ?? null;
    if (insertCourse($courseName, $professorID, $termOffered, $conn)) {
        echo COURSE_ADDED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn->close();


