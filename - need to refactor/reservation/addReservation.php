<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/manageReservationRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('courseID') && isValidPostVar('bookISBN') && isValidPostVar('numCopies'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser()) {
    $bookISBN = $_POST['bookISBN'];
    $courseID = $_POST['courseID'];
    $numCopies = $_POST['numCopies'];
    $professorID = /* TODO From session if prof otherwise get from post if librarian */;
    if (insertReservation($courseID, $bookISBN, $conn)) {
        echo addedReservationReturn($bookISBN, $numCopies);
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn->close();


