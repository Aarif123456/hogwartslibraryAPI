<?php

require_once 'librarianHelper.php';
librarianEndpointSetup();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $userID = (int)$_POST['userID'];
    $debug = DEBUG;
    if (insertLibrarian($userID, $conn, $debug)) {
        echo librarianCreated($userID);
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


