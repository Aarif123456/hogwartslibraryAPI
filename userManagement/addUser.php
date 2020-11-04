<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/registerUserRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('fname') && isValidPostVar('lname')
      && isValidPostVar('userType') && isValidPostVar('passcode'))) {
    exit(MISSING_PARAMETERS);
}
if (strcmp($_POST['passcode'], REGISTRATION_PASSCODE) != 0) {
    exit(INVALID_PASSCODE);
}

if (checkSessionInfo() && validateHeadmaster()) {
    /* Get user info in a object */
    $user = (object)[
        'fname' => trim($_POST['fname']),
        'lname' => trim($_POST['lname']),
        'userType' => trim($_POST['userType'])
    ];
    updateUserCategory($user);
    /* If we have the information needed to create new account then we will make it */
    $account = null;
    if (isValidPostVar('username') && isValidPostVar('password')) {
        $account = (object)[
            'username' => $_POST['username'],
            'password' => $_POST['password'],
        ];
    }

    $userID = insertUser($user, $account, $conn);

    if (!(empty($userID))) {
        echo userCreated($userID);
        echo '<br>';
        echo userTypeCreated($user->userType);
    } else {
        echo COMMAND_FAILED;
    }
} else {
    redirectToLogin();
}

$conn->close();

function updateUserCategory($user)
{
    $userType = $user->userType;
    switch ($userType) {
        case 'student':
            if (isValidPostVar('major') && isValidPostVar('house')) {
                $user->major = trim($_POST['major']);
                $user->house = trim($_POST['house']);
            } else {
                exit(MISSING_PARAMETERS);
            }
            break;
        case 'professor':
            if (isValidPostVar('department')) {
                $user->department = trim($_POST['department']);
            } else {
                exit(MISSING_PARAMETERS);
            }
            break;
        default:
            break;
    }
}


