<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
/* Used to double check that the headmaster is the one resetting the password */
require_once '../repository/headmasterVerifyRepo.php';
require_once '../repository/resetPassword.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('uID') && isValidPostVar('uPassword')
      && isValidPostVar('developerPassword'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster()) {
    $developerPassword = $_POST['developerPassword'];
    $developerID = $_SESSION['userID'];
    $result = queryDeveloperPassword($developerID, $conn);
    $row = $result->fetch_assoc();
    $password = $_POST['uPassword'];
    $uID = $_POST['uID'];
    /* Make sure the password is correct */
    if ($result->num_rows != 0 && password_verify($developerPassword, $row['password'])) {
        /* Reset invalid password count, because admin entered the correct password */
        $_SESSION['invalidPasswordCount'] = 0;
        if (resetQuery($uID, $password, $conn)) {
            echo passwordReset($uID);
        }
    } else {
        wrongDeveloperPassword();
    }
} else {
    redirectToLogin();
}


$conn->close();
function wrongDeveloperPassword()
{
    $_SESSION['invalidPasswordCount'] = ($_SESSION['invalidPasswordCount'] ?? 0) + 1;
    if ($_SESSION['invalidPasswordCount'] > 3) {
        redirectToLogin();
    }
    exit(HEADMASTER_VERIFY_FAIL);
}


