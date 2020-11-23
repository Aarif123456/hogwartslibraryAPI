<?php
//verify login

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/secretKey.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;

function getAuth($conn)
{
    $config = new PHPAuthConfig($conn);

    return new PHPAuth($conn, $config);
}

/*Make sure user is validated */
function validateUser($conn): bool
{
    $auth = getAuth($conn);
    if ($auth->isLogged()) {
        return (int)$auth->getCurrentUID() === (int)($_SESSION['userID']);
    }

    return false;
}

function getUserID($conn): int
{
    $auth = getAuth($conn);

    return $auth->getCurrentUID();
}

function login($loginInfo, $conn, $debug = false)
{
    $auth = getAuth($conn);
    $result = $auth->login($loginInfo->email, $loginInfo->password, $loginInfo->remember);
    if ($debug) {
        echo $result['message'];
        if (!$result['error']) {
            echo 'br />';
            echo $result['hash'];
        }
    }

    return !$result['error'];
}

function logout($conn): bool
{
    $auth = getAuth($conn);

    return $auth->logout($auth->getCurrentSessionHash());
}

function checkSessionInfo(): bool
{
    return isset($_SESSION['userID']) && isset($_SESSION['userType']);
}

/* logout function */
function redirectToLogin()
{
    header('HTTP/1.0 403 Forbidden');
    exit(UNAUTHORIZED_NO_LOGIN);
}

/* Utility functions to check user's type */
function isProfessor(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ''), 'professor') == 0;
}

function isStudent(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ''), 'student') == 0;
}

function isHeadmaster(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ''), 'headmaster') == 0;
}

function isLibrarian(): bool
{
    return strcmp(trim($_SESSION['userType'] ?? ''), 'librarian') == 0;
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