<?php
//verify login

require_once 'constants.php';
require_once 'secretKey.php';
/*require_once '../vendor/autoload.php';

use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;

function getAuth($conn)
{
    $config = new PHPAuthConfig($conn);

    return new PHPAuth($conn, $config);
}*/

/*Make sure user is validated */
/*function validateUser($conn): bool
{
    $auth = getAuth($conn);
    if ($auth->isLogged()) {
        return $auth->getCurrentUID() === (int)($_SESSION['userID']);
    }

    return false;
}*/

function validateUser($conn)
{ //verify user did not fake authentication
    $cookie = $_COOKIE['rememberMe'] ?? '';
    if ($cookie) {
        [$userID, $token, $mac] = explode(':', $cookie); //get info from cookie
        if (!(hash_equals(hash_hmac('sha256', $userID . ':' . $token, SECRET_KEY), $mac))) {

            return false;
        }
        $userToken = $_SESSION['token']; //**  vulnerable if session was compromised

        return hash_equals($userToken, $token);
    }


    return false;
}
/*
function getUserID($conn): int
{
    $auth = getAuth($conn);

    return $auth->getCurrentUID();
}

function login($email, $password, $remember, $conn)
{
    $auth = getAuth($conn);

    return $auth->login($email, $password, $remember);
}

function checkUsername($email, $conn): bool
{
    $auth = getAuth($conn);

    return $auth->isEmailTaken($email);
}*/

function logout($conn): bool
{
    /*$auth = getAuth($conn);

    return $auth->logout($auth->getCurrentSessionHash());*/
    // TODO remove this
    return destroy_session_and_data();
}

function checkSessionInfo(): bool
{
    return isset($_SESSION['userID']) && isset($_SESSION['userType']);
}

/* logout function */
function redirectToLogin()
{
    header('HTTP/1.0 403 Forbidden');
    destroy_session_and_data();
    exit(UNAUTHORIZED_NO_LOGIN);
}

/* Utility functions to check user's type */
function isProfessor(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ""), "professor") == 0;
}

function isStudent(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ""), "student") == 0;
}

function isHeadmaster(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ""), "headmaster") == 0;
}

function isLibrarian(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ""), "librarian") == 0;
}

/* utility function to make sure user has the correct permission*/
function validateStudent($conn): bool
{
    return isStudent() && validateUser($conn);
}

function validateProfessor($conn): bool
{
    return isProfessor() && validateUser($conn);
}

function validateHeadmaster($conn): bool
{
    return isHeadmaster() && validateUser($conn);
}

function validateLibrarian($conn): bool
{
    return isLibrarian() && validateUser($conn);
}

/* function to destroy session */
function destroy_session_and_data()
{
    $_SESSION = [];
    setcookie(session_name(), '', time() - 1, '/');
    $cookie = $_COOKIE['rememberMe'] ?? '';
    if ($cookie) {
        [$userID, $token, $mac] = explode(':', $cookie); //get info from cookie
        setcookie(
            'rememberMe',
            $userID . ':' . $token . ':' . $mac,
            time() - 1,
            '/',
            'arif115.myweb.cs.uwindsor.ca',
            true,
            true
        );
        unset($_COOKIE['rememberMe']);
    }
    session_destroy();
    return true;
}
