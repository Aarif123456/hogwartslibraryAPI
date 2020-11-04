<?php
//Abdullah
//load reservations for professor
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}
require_once 'login.php';
require_once 'authenticate.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) {
    exit('Error connecting to database');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Allow error handling instead of exception handling
$conn->set_charset("utf8mb4");
if (isset($_SESSION['userID']) && isset($_SESSION['librarian']) &&
    isset($_SESSION['token']) && isset($_SESSION['userType'])) {
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
            $reserveStmt = $conn->prepare("DELETE FROM `reservations` WHERE `reservedFor` = ? AND `bookISBN` = ? ");
            $reserveStmt->bind_param("is", $courseID, $bookISBN);
            $reserveStmt->execute();
            echo "book reservation deleted";
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
    $profStmt = $conn->prepare("SELECT professorID  FROM courses WHERE courseID =?");
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




