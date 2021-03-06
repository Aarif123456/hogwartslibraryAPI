<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/registerUserRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (!(isValidPostVar('fname') && isValidPostVar('lname')
      && isValidPostVar('userType') && isValidPostVar('passcode')
      && isValidPostVar('username') && isValidPostVar('password'))) {
    exit(MISSING_PARAMETERS);
}
if (strcmp($_POST['passcode'], REGISTRATION_PASSCODE) != 0) {
    exit(INVALID_PASSCODE);
}

$debug = DEBUG;

/* Get user info in a object */
$user = (object)[
    'fname' => trim($_POST['fname']),
    'lname' => trim($_POST['lname']),
    'userType' => trim($_POST['userType'])
];
updateUserCategory($user);
/* If we have the information needed to create new account then we will make it */
$account = null;
$account = (object)[
    'email' => $_POST['username'] ?? '',
    'password' => $_POST['password'] ?? '',
];

$userID = insertUser($user, $account, $conn, $debug);

if (!(empty($userID))) {
    echo userCreated($userID);
    echo '<br>';
    echo userTypeCreated($user->userType);
} else {
    echo COMMAND_FAILED;
}

$conn = null;

function updateUserCategory($user)
{
    $userType = $user->userType;
    switch ($userType) {
        case 'student':
            if (isValidPostVar('major') && isValidPostVar('house') && isValidHouse($_POST['house'])) {
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


