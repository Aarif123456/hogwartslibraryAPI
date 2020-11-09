<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userHoldRepo.php';
require_once '../repository/verifyUserEligibility.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser()) {
    $holderID = isLibrarian() ? $_POST['userID'] ?? "" : $_SESSION['userID'];
    $bookISBN = $_POST['bookISBN'] ?? "";
    $courseID = $_POST['courseID'] ?? "";
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
    createHold($bookISBN, $holderID, $conn);
    // Reserve copy
    if (reserveCopy($bookISBN, $holderID, $courseID, $conn)) {
       echo HOLD_READY_FOR_PICKUP;
    } else {
        echo BOOK_PLACED_ON_HOLD;
    }
} else {
    redirectToLogin();
}

$conn->close();
