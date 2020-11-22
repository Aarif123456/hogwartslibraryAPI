<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userCheckoutRepo.php';

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
    $librarianID = $_SESSION['userID'];
    verifySelfCheckout($librarianID, $borrowedBy);
    /* Make sure user is not blacklisted or above their limit */
    $blackListLimitResult = checkUserEligibleForCheckout($borrowedBy, $conn);
    if (empty($blackListLimitResult)) {
        exit(USER_INELIGIBLE_FOR_CHECKOUT);
    }
    /* Check out book if valid */
    if (!checkOutBook($bookBarcode, $borrowedBy, $librarianID, $conn)) {
        exit(BOOK_INELIGIBLE_FOR_CHECKOUT);
    }
    echo successfulCheckout($bookBarcode, $borrowedBy, $librarianID);
} else {
    redirectToLogin();
}


