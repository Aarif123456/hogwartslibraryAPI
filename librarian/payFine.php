<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/userFinesRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('userID') && isValidPostVar('pay'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian($conn)) {
    $userID = $_POST['userID'];
    $pay = $_POST['pay'];
    $librarianID = getUserID($conn);
    $debug = DEBUG;
    verifySelfCheckout($librarianID, $pay);
    if (payFine($pay, $userID, $conn, $debug)) {
        echo PAID_SUCCESSFULLY;
    } else {
        echo INTERNAL_SERVER_ERROR;
    }
} else {
    redirectToLogin();
}

$conn = null;
