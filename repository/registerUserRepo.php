<?php

/* Imports */
require_once 'error.php';

function insertUser($user, $account, $conn, $debug = false)
{
    try {
        $conn->autocommit(false);
        /* store user info in member table */
        $id = storeMemberInfo($user, $conn);
        /* Store information specific to the user type in the appropriate table */
        storeUserTypeInfo($id, $user, $conn);
        /* Store the user's login info */
        storeUserAccountInfo($id, $account, $conn);
        $conn->autocommit(true);

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

function storeMemberInfo($user, $conn)
{
    $stmt = $conn->prepare("INSERT INTO members (fname, lname) VALUES (?, ?)");
    $stmt->bind_param("ss", $user->fname, $user->lname);
    $stmt->execute();
    $id = $conn->insert_id;
    $stmt->close();

    return $id;
}

/* Insert user account info */
function storeUserAccountInfo($id, $account, $conn)
{
    if (!(empty($account))) {
        $stmt = $conn->prepare("INSERT INTO userAccount (userID, userName,password) VALUES (?, ?, ?)");
        /* hash password and store */
        $password = password_hash($account->password, PASSWORD_DEFAULT);
        //password_verify will make sure it is correct without having to store it
        $stmt->bind_param("iss", $id, $account->username, $password);
        $stmt->execute();
        $stmt->close();
    }
}

function storeUserTypeInfo($id, $user, $conn)
{
    $userType = $user->userType;
    switch ($userType) {
        case 'student':
            storeStudentInfo($id, $user, $conn);
            break;
        case 'professor':
            storeProfessorInfo($id, $user, $conn);
            break;
        case 'headmaster':
            storeHeadmasterInfo($id, $user, $conn);
            break;
        default:
    }

    return "";
}

function storeStudentInfo($id, $user, $conn)
{
    if (!(empty($user->house) || empty($user->major))) {
        $stmt = $conn->prepare("INSERT INTO students (studentID,major, house) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $user->major, $user->house);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception(MISSING_PARAMETER_FOR_USER_TYPE, 1);
    }
}

function storeProfessorInfo($id, $user, $conn)
{
    if (!(empty($user->department))) {
        $stmt = $conn->prepare("INSERT INTO professor (professorID, department) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $user->department);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception(MISSING_PARAMETER_FOR_USER_TYPE, 1);
    }
}

function storeHeadmasterInfo($id, $user, $conn)
{
    $stmt = $conn->prepare("INSERT INTO headmasters (headmasterID) VALUES (?)");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

