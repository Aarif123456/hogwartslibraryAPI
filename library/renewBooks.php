<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/verifyUserEligibility.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/renewRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $renewerID = isLibrarian() ? $_POST['userID'] ?? '' : $_SESSION['userID'];
    $bookBarcode = $_POST['bookBarcode'] ?? '';
    $debug = DEBUG;
    if (empty($bookBarcode) || empty($renewerID)) {
        exit(MISSING_PARAMETERS);
    }
    if (checkUserBlacklisted($renewerID, $conn)) {
        exit(USER_BLACKLISTED);
    }

    if (renewBook($renewerID, $bookBarcode, $conn, $debug)) {
        echo BOOK_RENEWED;
    } else {
        echo BOOK_MAXIMUM_RENEWED;
        if ($debug) {
            echo BOOK_RENEW_FAILURE_DEBUG;
        }
    }
} else { //Not logged in
    redirectToLogin();
}

$conn = null;


