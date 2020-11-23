<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/userCheckoutRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('bookBarcode') && isValidPostVar('borrowedBy'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian($conn)) {
    $bookBarcode = $_POST['bookBarcode'];
    $borrowedBy = $_POST['borrowedBy'];
    $librarianID = getUserID($conn);
    $debug = DEBUG;
    verifySelfCheckout($librarianID, $borrowedBy);
    /* Make sure user is not blacklisted or above their limit */
    $blackListLimitResult = checkUserEligibleForCheckout($borrowedBy, $conn);
    if (empty(count($blackListLimitResult))) {
        exit(USER_INELIGIBLE_FOR_CHECKOUT);
    }
    /* Check out book if valid */
    if (!checkOutBook($bookBarcode, $borrowedBy, $librarianID, $conn, $debug)) {
        exit(BOOK_INELIGIBLE_FOR_CHECKOUT);
    }
    echo successfulCheckout($bookBarcode, $borrowedBy, $librarianID);
} else {
    redirectToLogin();
}


