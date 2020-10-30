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
if($conn->connect_error) {
  exit('Error connecting to database'); 
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Allow error handling instead of exception handling
$conn->set_charset("utf8mb4");
if (isset($_SESSION['userID'])  && isset($_SESSION['librarian']) && 
    isset($_SESSION['token']) && isset($_SESSION['userType']))   
  {
  if(validateLibrarian($_SESSION['userID'])){
    if(!(isset($_POST['userID']))){
      exit("no user is selected");
    }
    $userID =$_POST['userID'];
    //echo "Am librarian holding on behalf of $userID";
  }   
  elseif(validateProfessor($_SESSION['userID'])){
    $userID = $_SESSION['userID']; 
  }
  elseif(!(validateProfessor($_POST['userID'])) && !($_SESSION['librarian'])) {
    redirectToLogin();
  }
  else{
    echo "I am no one";
    exit();
  }
  $coursesStmt = $conn->prepare("SELECT courseID, courseName FROM courses WHERE professorID = ?");
  //find all transaction 
  $coursesStmt->bind_param("i", $userID);
  $coursesStmt->execute();
  $result = $coursesStmt->get_result();
  if(!$result) {
    exit ("Unable to get member info. Query failed");
  }
  $arr=array();
  $count=0;
  while($row = $result->fetch_object()) {
    $arr['course' . $count]['courseInfo'] = $row;
    $reservationStmt = $conn->prepare("SELECT bookISBN,bookName,COUNT(*) as cnt FROM bookItem NATURAL JOIN  books WHERE reservedFor = ? GROUP BY bookItem.bookISBN");
    //SELECT CONCAT(reservedFor,bookISBN) AS reserveID,reservedFor,bookISBN,bookName,COUNT(*) as cnt FROM bookItem NATURAL JOIN books WHERE reservedFor IN ('9','10','11','12') GROUP BY reserveID ORDER BY reservedFor

    //get bookInfo and number of bookCopies reserved
    $reservationStmt->bind_param("i", $row->courseID);
    $reservationStmt->execute();
    $booksList= $reservationStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $arr['courseID ' + $row->courseID + ' - ' $row->courseName]= $booksList;
    $count++;
  }
  //SELECT reservedFor,bookISBN,bookName,COUNT(*) as cnt FROM bookItem NATURAL JOIN  books WHERE reservedFor IN ('9','10','11','12')    GROUP BY bookItem.bookISBN ORDER BY reservedFor
  if(!$arr) exit('No rows');
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

