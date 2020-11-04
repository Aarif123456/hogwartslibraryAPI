<?php

/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && isset($_SESSION['userType'])) {
    if (validateLibrarian()) {
        if (!(isset($_POST['userID']))) {
            exit("no user is selected");
        }
        $userID = $_POST['userID'];
    } //not a librarian but a valid professor
    elseif (validateProfessor() && !($_SESSION['librarian'])) {
        $userID = $_SESSION['userID'];
    } else {
        redirectToLogin();
    }
    //Make sure we have all the info we need
    if (isset($_POST['courseID']) && isset($_POST['bookISBN'])) {
        $courseID = $_POST['courseID'];
        $bookISBN = $_POST['bookISBN'];
        if (checkProf($userID, $courseID, $conn)) {  //check if professor is teaching the course
            try {
                $conn->autocommit(false);
                //delete reservations before inserting if it exists TODO - change to update
                deleteReservation($courseID, $bookISBN, $conn);
                if (isset($_POST['add']) && isset($_POST['numCopies'])) { //if in add mode
                    $numCopies = checkCopies($_POST['numCopies'], $bookISBN, $conn);
                    addReservation($courseID, $bookISBN, $numCopies, $conn);
                    $conn->autocommit(true); //commit delete and insert
                } elseif (isset($_POST['delete'])) { //in delete mode
                    $conn->autocommit(true); //commit delete
                    echo "book reservation deleted";
                } else { //failed too add
                    $conn->rollback(); //if we can't add then roll back
                    exit("Missing values... cannot add reservation");
                }
            } catch (Exception $e) {
                $conn->rollback(); //remove all queries from queue if error (undo)
                echo "Please enter a valid ISBN number";
            }
        } else {
            exit("the professor is not teaching the course");
        }
    } else {
        exit("not enough info to reserve");
    }
} else {
    redirectToLogin();
}
$conn->close();
function checkProf($userID, $courseID, $conn)
{
    $profStmt = $conn->prepare("SELECT professorID FROM `courses` WHERE courseID =? AND professorID >0");
    //find all transaction
    $profStmt->bind_param("i", $courseID);
    $profStmt->execute();
    $result = $profStmt->get_result();
    $result = $result->fetch_assoc();
    if (!$result) {
        exit("this course does not exist");
    }

    return $result['professorID'] == $userID;
}

function checkCopies($numCopies, $bookISBN, $conn)
{
    if ($numCopies > 10) { //can not reserve more than 10 copies
        $numCopies = 10;
    }
    if ($numCopies < 1) {
        $numCopies = 1;
    }
    $profStmt = $conn->prepare("SELECT Count(*) as num FROM bookItem WHERE bookISBN = ? AND reservedFor IS NULL");
    $profStmt->bind_param("s", $bookISBN);
    $profStmt->execute();
    $result = $profStmt->get_result();
    $result = $result->fetch_assoc();
    if (!$result) {
        exit("this course does not exist");
    }
    if ($result['num'] <= 0) {
        exit("there are no available copies to reserve for this book:(");
    }

    return ($result['num'] <= $numCopies) ? $result['num'] : $numCopies;
}

function deleteReservation($courseID, $bookISBN, $conn)
{
    $deleteStmt = $conn->prepare("UPDATE bookItem SET reservedFor = NULL WHERE `reservedFor` = ? AND `bookISBN` = ?");
    $deleteStmt->bind_param("is", $courseID, $bookISBN);
    $deleteStmt->execute();
}

function addReservation($courseID, $bookISBN, $numCopies, $conn)
{
    $reserveStmt = $conn->prepare(
        "UPDATE bookItem SET reservedFor = ? WHERE reservedFor IS NULL AND bookISBN = ? LIMIT ?"
    );
    $reserveStmt->bind_param("isi", $courseID, $bookISBN, $numCopies);
    $reserveStmt->execute();
    echo "book successfully reserved $numCopies copies";
}



