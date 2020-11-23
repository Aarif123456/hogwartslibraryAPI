<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
/* Used to double check that the headmaster is the one resetting the password */
require_once __DIR__ . '/../vendor/autoload.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('uID') && isValidPostVar('uPassword')
      && isValidPostVar('developerPassword'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateHeadmaster($conn)) {
    $developerPassword = $_POST['developerPassword'];
    $developerID = getUserID($conn);
    $password = $_POST['uPassword'];
    $uID = $_POST['uID'];
    $debug = DEBUG;
    $auth = getAuth($conn);
    /* Make sure the password is correct */
    if ($auth->comparePasswords($developerID, $developerPassword)) {
        $userInfo = $auth->getUser($uID);
        $resetInfo = $auth->requestReset($userInfo['email']);
        if (!$resetInfo['error']) {
            $token = $resetInfo['token'];
            $resetPasswordResult = $auth->resetPass($token, $password, $password);
            if (!$resetPasswordResult['error']) {
                echo passwordReset($uID);
            } else {
                exit($resetPasswordResult['message']);
            }
        } else {
            exit($resetInfo['message']);
        }
    } else {
        exit(HEADMASTER_VERIFY_FAIL);
    }
} else {
    redirectToLogin();
}


$conn = null;
