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

if (checkSessionInfo() && 
    isset($_SESSION['userType']) && isset($_POST['listType'])) {
    if(validateLibrarian($_SESSION['userID'])){
      if(!(isset($_POST['userID']))){
        exit("no user is selected");
      }
      $userID =$_POST['userID'];
      //echo "Am librarian holding on behalf of $userID";
    }
    elseif(validateUser($_SESSION['userID'])){
      $userID = $_SESSION['userID']; 
    }
    elseif(!(validateUser($_POST['userID'])) && !($_SESSION['librarian'])) {
      redirectToLogin();
    }
    else{
      echo "I am no one";
      exit();
    } 
    $listType = $_POST['listType'];  
    if(strcmp($listType, "loadReservedBooks")==0 && isset($_POST['courseID'])){ //load books per course
        $stmt = $conn->prepare("SELECT `bookISBN`,`bookName`,`author`,`numCopies`FROM bookItems NATURAL JOIN books WHERE `reservedFor`=? ");
        //find all transaction 
        $stmt->bind_param("i", $_POST['courseID']);
    }
    elseif(strcmp($listType, "loadCourses")==0){
        if(strcmp($_SESSION['userType'],"professor")==0 || $_SESSION['librarian']) { //if teacher load
            $stmt = $conn->prepare("SELECT `courseID`,`courseName`FROM `courses` WHERE `professorID`=?");
        }
        else{
            $stmt = $conn->prepare("SELECT `courseID`, `courseName` FROM `courseEnrolled` NATURAL JOIN `courses` WHERE `studentID` = ?");
        }
        $stmt->bind_param("i", $userID);
    }
    elseif(strcmp($listType, "loadAvailableBooks")==0){
        $stmt = $conn->prepare("SELECT `bookISBN`,`bookName`,`author`FROM `books` WHERE `totalCopies`>0");
    }
    elseif(strcmp($listType, "loadProfessor")==0){
        $stmt = $conn->prepare("SELECT `professorID`,`fname`,`lname` FROM `professor` INNER JOIN `members` ON professorID= memberID WHERE professorID>0 ORDER BY professorID"); 
    }
    else{
        exit("Incorrect list Type:(");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if(!$result) {
      exit (QUERY_FAILURE);
    }
    $arr = $result->fetch_all(MYSQLI_ASSOC);
    if(!$arr) exit(NO_ROWS_RETURNED);
    echo json_encode($arr);
    $result->close();
}
else{
  //echo "Session not set";
  //var_dump($_SESSION);
  redirectToLogin();
}

$conn->close();
?>

