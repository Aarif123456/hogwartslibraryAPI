<?php
//Abdullah
//load reservations for students
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
  if(!(validateUser($_SESSION['userID']))){
    redirectToLogin();
  }
  $userID = $_SESSION['userID']; 
  $coursesStmt = $conn->prepare("SELECT courseID, courseName FROM  courseEnrolled NATURAL JOIN courses WHERE studentID = ?");
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
    //get bookInfo and number of bookCopies reserved
    $reservationStmt->bind_param("i", $row->courseID);
    $reservationStmt->execute();
    $booksList= $reservationStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $arr['course' . $count]['reservedBookInfo']= $booksList;
    $count++;
  }
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

