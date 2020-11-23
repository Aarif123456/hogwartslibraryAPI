<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/lostRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('bookBarcode'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser($conn)) {
    $userID = getUserID($conn);
    $bookBarcode = $_POST['bookBarcode'];
    $debug = DEBUG;
    if (reportLostBook($userID, $bookBarcode, $conn, $debug)) {
        echo(BOOK_LOST_RETURN);
    } else {
        exit(UNAUTHORIZED_NOT_OWNER);
    }
} else { //Not logged in
    redirectToLogin();
}

$conn = null;
