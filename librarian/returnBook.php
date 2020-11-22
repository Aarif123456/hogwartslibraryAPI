<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userReturnRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('bookBarcode'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian($conn)) {
    $todayDate = date("Y-m-d");
    $bookBarcode = $_POST['bookBarcode'];
    $librarianID = $_SESSION['userID'];
    $debug = false;
    /* If book is marked as lost then change book status and update member to refund 85% of their money */
    if (refundLostBook($librarianID, $bookBarcode, $conn, $debug)) {
        echo BOOK_FOUND;
        echo '<br>';
    } else {
        if (!returnBook($todayDate, $librarianID, $bookBarcode, $conn, $debug)) {
            echo(RETURN_FAILED);
        } else {
            echo successfulReturn($todayDate, $bookBarcode);
        }
    }
} else {
    redirectToLogin();
}

$conn = null;



