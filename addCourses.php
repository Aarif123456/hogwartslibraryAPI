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
require_once 'repository/manageCourseRepo.php';

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


