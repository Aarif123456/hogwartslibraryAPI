<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/cancelHoldRepo.php';
require_once '../repository/verifyUserEligibility.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $holderID = isLibrarian() ? $_POST['userID'] ?? '' : $_SESSION['userID'];
    $holdID = $_POST['holdID'] ?? '';
    $debug = DEBUG;
    if (empty($holdID) || empty($holderID)) {
        exit(MISSING_PARAMETERS);
    }
    if (checkUserBlacklisted($holderID, $conn)) {
        exit(USER_BLACKLISTED);
    }
    // TODO add update table on return, hold and cancel hold - since that's when you get a reserved copy 

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


