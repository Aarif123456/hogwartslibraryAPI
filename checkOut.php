<?php

/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/userCheckoutRepo.php';

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('bookBarcode') && isValidPostVar('borrowedBy'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian()) {
    $bookBarcode = $_POST['bookBarcode'];
    $borrowedBy = $_POST['borrowedBy'];
    $librarianID = $_SESSION['userID'];
    verifySelfCheckout($librarianID, $borrowedBy);
    /* Make sure user is not blacklisted or above their limit */
    $blackListLimitResult = checkUserEligibleForCheckout($borrowedBy, $conn);
    if (empty($blackListLimitResult->fetch_all(MYSQLI_ASSOC))) {
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


