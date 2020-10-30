<?php
/* Imports */
require_once 'error.php';

function queryUserVerify($username, $userType, $conn){
    // get query
    $query = getQueryForUserType($userType);
    // if no query return back null
    if(empty($query)) return null;
    /* Prepare statement */
    $stmt = $conn->prepare($query);
    bindUserVerifyQuery($username, $userType, $stmt);
    // otherwise return back result from query
    return getExecutedResult($stmt);
}

function bindUserVerifyQuery($username, $userType, $stmt){
    if(strcmp($userType,"user")==0){
        $stmt->bind_param("sss", $username, $username, $username);
    }
    else{
        $stmt->bind_param("s", $username);
    }
}

function getQueryForUserType($userType){
    switch ($userType){
        case "user":
            return "SELECT userID,fname,lname,password, 'student' as userType FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN students ON `userID` = `studentID` WHERE userName = ? UNION  SELECT userID,fname,lname,password,'professor' as userType FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN professor ON memberID=professorID WHERE userName = ? UNION SELECT userID,fname,lname,password,'headmaster' as userType FROM userAccount INNER JOIN members ON `userID` = `memberID`INNER JOIN headmasters ON memberID=headmasterID WHERE userName = ?";
        case "student":
            return "SELECT userID,fname,lname,password FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN students ON `userID` = `studentID` WHERE userName = ?";
        case "professor":
            return "SELECT userID,fname,lname,password FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN professor ON `userID` = `professorID` WHERE userName = ?";
        case "headmaster":
            return "SELECT userID,fname,lname,password FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN headmaster ON `userID` = `headmasterID` WHERE userName = ?";
        case "librarian":
            return "SELECT userID,fname,lname,password FROM userAccount INNER JOIN members ON `userID` = `memberID` INNER JOIN librarianAccount ON `userID` = `librarianID` WHERE userName = ?";
        default:
            return "";  
    }
    return "";    
}

?>