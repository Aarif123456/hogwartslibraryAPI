<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/manageCourseRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('courseName') && isValidPostVar('professorID'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $professorID = $_POST['professorID'];
    $courseName = $_POST['courseName'];
    $termOffered = isValidPostVar('TermOffered') ?? null;
    $debug = DEBUG;
    if (insertCourse($courseName, $professorID, $termOffered, $conn, $debug)) {
        echo COURSE_ADDED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


