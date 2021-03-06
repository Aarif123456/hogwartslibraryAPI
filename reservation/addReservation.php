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

if (!(isValidPostVar('courseID') && isValidPostVar('bookISBN') && isValidPostVar('numCopies'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser($conn)) {
    $debug = DEBUG;
    $bookISBN = $_POST['bookISBN'];
    $courseID = $_POST['courseID'];
    $numCopies = $_POST['numCopies'];
    $professorID = getProfessorID();
    if (!deleteReservation($professorID, $courseID, $bookISBN, $conn, $debug)) {
        exit(RESERVATION_RESET_FAILED);
    }
    $actuallyReserved = insertReservation($courseID, $bookISBN, $professorID, $numCopies, $conn, $debug);
    if ($actuallyReserved > 0) {
        /* TODO add in warning if numCopies does not match actuallyReserved */
        echo addedReservationReturn($bookISBN, $actuallyReserved);
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


