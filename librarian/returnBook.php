<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/userReturnRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('bookBarcode'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateLibrarian($conn)) {
    $todayDate = date('Y-m-d');
    $bookBarcode = $_POST['bookBarcode'];
    $librarianID = getUserID($conn);
    $parameter = (object)[
        'todayDate' => $todayDate,
        'bookBarcode' => $bookBarcode,
        'librarianID' => $librarianID
    ];
    $debug = DEBUG;
    /* If book is marked as lost then change book status and update member to refund 85% of their money */
    if (refundLostBook($parameter, $conn, $debug)) {
        echo BOOK_FOUND;
        echo '<br>';
    } else {
        if (!returnBook($parameter, $conn, $debug)) {
            echo(RETURN_FAILED);
        } else {
            echo successfulReturn($todayDate, $bookBarcode);
        }
    }
} else {
    redirectToLogin();
}

$conn = null;



