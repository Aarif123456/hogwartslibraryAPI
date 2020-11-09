<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/userFinesRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('userID') && isValidPostVar('pay'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian()) {
    $userID = $_POST['userID'];
    $pay = $_POST['pay'];
    $librarianID = $_SESSION['userID'];
    verifySelfCheckout($librarianID, $pay);
    if (payFine($pay, $userID, $conn)) {
        echo PAID_SUCCESSFULLY;
    } else {
        echo INTERNAL_SERVER_ERROR;
    }
} else {
    redirectToLogin();
}

$conn->close();
