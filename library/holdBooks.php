<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/userHoldRepo.php';
require_once __DIR__ . '/../repository/verifyUserEligibility.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $holderID = isLibrarian() ? $_POST['userID'] ?? '' : getUserID($conn);
    $bookISBN = $_POST['bookISBN'] ?? '';
    $courseID = $_POST['courseID'] ?? '';
    $debug = DEBUG;
    if (empty($bookISBN) || empty($holderID)) {
        exit(MISSING_PARAMETERS);
    }
    if (checkUserBlacklisted($holderID, $conn)) {
        exit(USER_BLACKLISTED);
    }

    //make user does not have a previous hold on it
    if (holdExists($bookISBN, $holderID, $conn)) {
        exit(HOLD_EXISTS);
    }
    // create entry in hold table
    createHold($bookISBN, $holderID, $conn, $debug);
    // Reserve copy
    if (reserveCopy($bookISBN, $holderID, $courseID, $conn, $debug)) {
        echo HOLD_READY_FOR_PICKUP;
    } else {
        echo BOOK_PLACED_ON_HOLD;
    }
} else {
    redirectToLogin();
}

$conn = null;
