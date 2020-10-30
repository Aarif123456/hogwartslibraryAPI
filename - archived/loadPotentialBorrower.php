<?php
header('Access-Control-Allow-Origin: https://abdullaharif.tech'); 
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
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
if (isset($_SESSION['userID'])  && isset($_SESSION['librarian'])
  && isset($_SESSION['token'])){
  if(!(validateLibrarian($_SESSION['userID']))){
     echo "Not a librarian";
      redirectToLogin();
  }
  $arr=[];
  $librarianID = $_SESSION['userID']; 
  $stmt = $conn->prepare("SELECT memberID, fname, lname FROM members WHERE memberID != ? AND memberID !=0");
  //don't let librarian check out themselves or the headmaster
  $stmt->bind_param("i", $librarianID);
  $stmt->execute();
  $result = $stmt->get_result();
  if(!$result) {
    exit ("Unable to get member info. Query failed");
  }
  while($row = $result->fetch_object()) {
    $arr[] = $row;
  }
  if(!$arr) exit('No rows');
  echo json_encode($arr);
  $result->close();
} 
else{
  echo "Session not set";
  redirectToLogin();
}
$conn->close();
?>

