<?php

/* Imports */
require_once 'error.php';

function queryUserVerify($username, $userType, $conn)
{
    // get query
    $query = getQueryForUserType($userType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }
    /* Prepare statement */
    $stmt = $conn->prepare($query);
    bindUserVerifyQuery($username, $userType, $stmt);

    // otherwise return back result from query
    return getExecutedResult($stmt);
}

function bindUserVerifyQuery($username, $userType, $stmt)
{
    switch ($userType) {
        case "student":
            $stmt->bindValue(":studentEmail", $username, PDO::PARAM_STR);
            break;
        case "professor":
            $stmt->bindValue(":professorEmail", $username, PDO::PARAM_STR);
            break;
        case "headmaster":
            $stmt->bindValue(":headmasterEmail", $username, PDO::PARAM_STR);
            break;
        case "librarian":
            $stmt->bindValue(":librarianEmail", $username, PDO::PARAM_STR);
            break;
        default: // Case for user
            $stmt->bindValue(":studentEmail", $username, PDO::PARAM_STR);
            $stmt->bindValue(":professorEmail", $username, PDO::PARAM_STR);
            $stmt->bindValue(":headmasterEmail", $username, PDO::PARAM_STR);
    }
}

function getQueryForUserType($userType)
{
    $studentVerifyQuery = "
                            SELECT 
                                id,
                                fname,
                                lname,
                                password,
                                'student' as userType 
                            FROM 
                                phpauth_users 
                                INNER JOIN members ON id=memberID 
                                INNER JOIN students ON id=studentID 
                            WHERE 
                                email = :studentEmail";
    $professorVerifyQuery = "
                            SELECT 
                                id,
                                fname,
                                lname,
                                password,
                                'professor' as userType 
                            FROM 
                                phpauth_users 
                                INNER JOIN members ON id=memberID 
                                INNER JOIN professor ON memberID=professorID 
                            WHERE 
                                email = :professorEmail";
    $headmasterVerifyQuery = "
                            SELECT 
                                id,
                                fname,
                                lname,
                                password,
                                'headmaster' as userType 
                            FROM 
                                phpauth_users 
                                INNER JOIN members ON id=memberID 
                                INNER JOIN headmasters ON memberID=headmasterID 
                            WHERE 
                                email = :headmasterEmail";
    $librarianVerifyQuery = "
                            SELECT 
                                id,
                                fname,
                                lname,
                                password 
                            FROM 
                                phpauth_users 
                                INNER JOIN members ON `id` = `memberID` 
                                INNER JOIN librarianAccount ON `id` = `librarianID` 
                            WHERE 
                                email = :librarianEmail";
    switch ($userType) {
        case "user":
            return "$studentVerifyQuery UNION $professorVerifyQuery UNION $headmasterVerifyQuery";
        case "student":
            return $studentVerifyQuery;
        case "professor":
            return $professorVerifyQuery;
        case "headmaster":
            return $headmasterVerifyQuery;
        case "librarian":
            return $librarianVerifyQuery;
        default:
            return '';
    }
}

