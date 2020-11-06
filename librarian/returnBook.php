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

if (checkSessionInfo() && validateLibrarian()) {
    $todayDate = date("Y-m-d");
    $bookBarcode = $_POST['bookBarcode'];
    $librarianID = $_SESSION['userID'];
    /* If book is marked as lost then change book status and update member to refund 85% of their money */
    if (refundLostBook($bookBarcode, $conn)) {
        echo BOOK_FOUND;
        echo '<br>';
    } else {
        if (!returnBook($todayDate, $librarianID, $bookBarcode, $conn)) {
            echo(RETURN_FAILED);
        } else {
            echo succesfulReturn($todayDate, $bookBarcode);
        }
    }
} else {
    redirectToLogin();
}

$conn->close();



