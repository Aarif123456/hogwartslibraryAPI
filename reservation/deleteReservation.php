<?php

/* Imports */
require_once 'reservationHelper.php';
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/manageReservationRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('courseID') && isValidPostVar('bookISBN'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser()) {
    $professorID = getProfessorID();
    $debug = false;
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
$conn->close();
