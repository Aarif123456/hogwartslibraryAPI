<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPAuth\Auth as PHPAuth;
use PHPAuth\Config as PHPAuthConfig;

function insertUser($user, $account, $conn, $debug = false)
{
    try {
        $config = new PHPAuthConfig($conn);
        $auth = new PHPAuth($conn, $config);

        $conn->beginTransaction();
        $result = $auth->register($account->email, $account->password, $account->password);
        if($result['error']){
            exit($result['message']);
        }
        if($debug){
            echo $result['message'];
            echo "<br />";
        }
        /* Store the user's login info */
        $id = $auth->getUID($account->email);
        /* store user info in member table */
        storeMemberInfo($id, $user, $conn, $debug);
        /* Store information specific to the user type in the appropriate table */
        storeUserTypeInfo($id, $user, $conn, $debug);
        $conn->commit();

        return $id;
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        $conn->rollback();
        if ($debug) {
            debugPrint($e, $conn);
        }
        exit(WRITE_QUERY_FAILED);
    }
}

function storeMemberInfo($id, $user, $conn, $debug = false)
{
    $stmt = $conn->prepare('INSERT INTO members (memberID, fname, lname) VALUES (:id, :fname, :lname)');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':fname', $user->fname, PDO::PARAM_STR);
    $stmt->bindValue(':lname', $user->lname, PDO::PARAM_STR);

    return safeWriteQueries($stmt, $conn, $debug);
}

function storeUserTypeInfo($id, $user, $conn, $debug = false)
{
    $userType = $user->userType;
    switch ($userType) {
        case 'student':
            storeStudentInfo($id, $user, $conn, $debug);
            break;
        case 'professor':
            storeProfessorInfo($id, $user, $conn, $debug);
            break;
        case 'headmaster':
            storeHeadmasterInfo($id, $user, $conn, $debug);
            break;
        default:
    }

    return '';
}

function storeStudentInfo($id, $user, $conn, $debug = false)
{
    if (!(empty($user->house) || empty($user->major))) {
        $stmt = $conn->prepare('INSERT INTO students (studentID, major, house) VALUES (:id, :major, :house)');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':major', $user->major, PDO::PARAM_STR);
        $stmt->bindValue(':house', $user->house, PDO::PARAM_STR);
        safeWriteQueries($stmt, $conn, $debug);
    } else {
        throw new Exception(MISSING_PARAMETER_FOR_USER_TYPE, 1);
    }
}

function storeProfessorInfo($id, $user, $conn, $debug = false)
{
    if (!(empty($user->department))) {
        $stmt = $conn->prepare('INSERT INTO professor (professorID, department) VALUES (:id, :department)');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':department', $user->department, PDO::PARAM_STR);
        safeWriteQueries($stmt, $conn, $debug);
    } else {
        throw new Exception(MISSING_PARAMETER_FOR_USER_TYPE, 1);
    }
}

function storeHeadmasterInfo($id, $user, $conn, $debug = false)
{
    $stmt = $conn->prepare('INSERT INTO headmasters (headmasterID) VALUES (:id)');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    safeWriteQueries($stmt, $conn, $debug);
}

