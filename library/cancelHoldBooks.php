<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/cancelHoldRepo.php';
require_once __DIR__ . '/../repository/verifyUserEligibility.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $holderID = isLibrarian() ? $_POST['userID'] ?? '' : getUserID($conn);
    $holdID = $_POST['holdID'] ?? '';
    $debug = DEBUG;
    if (empty($holdID) || empty($holderID)) {
        exit(MISSING_PARAMETERS);
    }
    if (checkUserBlacklisted($holderID, $conn)) {
        exit(USER_BLACKLISTED);
    }
    // TODO add update expired hold table on return, hold and cancel hold - since that's when you get a reserved copy 

    if (!cancelHold($holdID, $holderID, $conn, $debug)) {
        exit(HOLD_NOT_EXISTS);
    }

    if (!reserveHoldCopy($holdID, $conn, $debug)) {
        exit(INTERNAL_SERVER_ERROR);
    }
    echo HOLD_CANCELLED_RETURN;
} else { //Not logged in
    redirectToLogin();
}

$conn = null;


