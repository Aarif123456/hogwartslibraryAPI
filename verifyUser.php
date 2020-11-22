<?php
/* program to verify login*/

require_once 'config/apiReturn.php';
require_once 'config/secretKey.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/verifyUserRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

/*if (validateUser($conn)) {
    exit(ALREADY_LOGGED_IN);
}*/
unset($_COOKIE['rememberMe']);
session_destroy();
session_start();
$_SESSION = [];

if (isValidPostVar('username') && isValidPostVar('userType') && isValidPostVar('password')) {
    /* Store user type in session */
    $userType = trim($_POST['userType']);
    $_SESSION['userType'] = $userType;
    $email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? 0;

    if (!(verifyUserType($userType))) {
        /* Exit and tell the client that their user type is invalid */
        exit(invalidUserType($userType));
    }
    $rows = queryUserVerify($email, $userType, $conn);
    $row = getArrayFromResult($rows);
    $loginInfo = (object)[
        'email' => $email,
        'password' => $password,
        'userType' => $userType,
        'remember' => $remember,
        'hashedPassword' => $row['password'],
    ];
    /* Make sure the password is correct */
    if (login($loginInfo, $conn)) {
        /* If we are determining user from table then determine it */
        $determineUser = strcmp($userType, 'user') == 0;
        if ($determineUser) {
            $_SESSION['userType'] = $row['userType'];
        }
        // $_SESSION['userID'] = getUserID($conn);
        $_SESSION['userID'] = $row['id'];
        $_SESSION['username'] = htmlentities($row['fname'] . ' ' . $row['lname']);
        storeUserAuthenticationInformation(getUserID($conn));
        echo authenticatedSuccesfully($_SESSION['userType']);
    } else {
        http_response_code(403);
        header('HTTP/1.0 403 Forbidden');
        echo INVALID_PASSWORD;
    }
} else {
    echo MISSING_PARAMETERS;
}

$conn = null;

function getArrayFromResult($rows)
{
    if (count($rows) == 0) {
        echo USERNAME_NOT_IN_TABLE;
    }

    return $rows[0];
}

/* store information regarding the user's login */
function storeUserAuthenticationInformation($userID)
{
    try {
        $token = random_bytes(128);
    } catch (Exception $e) {
        exit(INTERNAL_SERVER_ERROR);
    }
    $_SESSION['token'] = $token; //need some way for user to store token 
    $cookie = $userID . ':' . $token;
    $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
    $cookie .= ':' . $mac;
    setcookie('rememberMe', $cookie, time() + (86400 * 30), '/', 'arif115.myweb.cs.uwindsor.ca', true, true);
}


