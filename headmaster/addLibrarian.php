<?php

require_once 'librarianHelper.php';
librarianEndpointSetup();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $userID = (int)$_POST['userID'];
    if (insertLibrarian($userID, $conn)) {
        echo librarianCreated($userID);
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


