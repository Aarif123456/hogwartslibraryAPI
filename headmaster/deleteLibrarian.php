<?php

require_once 'librarianHelper.php';
librarianEndpointSetup();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('userID'))) {
    exit(MISSING_PARAMETERS);
}
if (!(is_numeric($_POST['userID']))) {
    exit(INVALID_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $userID = (int)$_POST['userID'];
    if (deleteLibrarian($userID, $conn)) {
        echo LIBRARIAN_DELETED;
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn = null;


