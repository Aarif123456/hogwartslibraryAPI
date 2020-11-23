<?php

/* Imports */
require_once __DIR__ . '/reservationHelper.php';
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/manageReservationRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('courseID') && isValidPostVar('bookISBN'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser($conn)) {
    $professorID = getProfessorID();
    $debug = DEBUG;
    $courseID = $_POST['courseID'];
    $bookISBN = $_POST['bookISBN'];
    if (deleteReservation($professorID, $courseID, $bookISBN, $conn, $debug)) {
        echo RESERVATION_DELETED;
    } else {
        exit(COMMAND_FAILED);
    }
} else {
    redirectToLogin();
}
$conn = null;
