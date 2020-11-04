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

$_SESSION = [];
unset($_COOKIE['rememberMe']);
session_destroy();
session_start();

if (isValidPostVar('username') && isValidPostVar('userType') && isValidPostVar('password')) {
    /* Store user type in session */
    $userType = trim($_POST['userType']);
    $_SESSION['userType'] = $userType;

    if (!(verifyUserType($userType))) {
        /* Exit and tell the client that their user type is invalid */
        exit(invalidUserType($userType));
    }
    $result = queryUserVerify($_POST['username'], $userType, $conn);
    $row = getArrayFromResult($result);

    /* Make sure the password is correct */
    if (password_verify($_POST['password'], $row['password'])) {
        /* If we are determining user from table then determine it */
        $determineUser = strcmp($userType, "user") == 0;
        if ($determineUser) {
            $_SESSION['userType'] = $row['userType'];
        }
        $_SESSION['username'] = htmlentities($row['fname'] . " " . $row['lname']);
        storeUserAuthenticationInformation($row['userID']);
        echo VALID_PASSWORD;
    } else {
        echo INVALID_PASSWORD;
    }
} else {
    echo MISSING_PARAMETERS;
}

$conn->close();

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
    setcookie('rememberMe', $cookie, time() + (86400 * 30), "/");
    $_SESSION['userID'] = $userID;
    ob_end_flush();
}


function getArrayFromResult($result)
{
    $row = $result->fetch_assoc();
    if ($result->num_rows == 0) {
        echo USERNAME_NOT_IN_TABLE;
    }

    return $row;
}


