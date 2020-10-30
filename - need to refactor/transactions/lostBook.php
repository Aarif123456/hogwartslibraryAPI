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
require_once 'verifyBookAvailability.php';
require_once 'repository/database.php';

/* Connect to database */
$conn = getConnection();

if (validateUser($_SESSION['userID']))   
{  
  $userID = $_SESSION['userID']; 
  //echo "User ID $userID";
  if(isset($_POST['bookBarcode']) && !(empty($_POST['bookBarcode']))) {
    $bookBarcode = $_POST['bookBarcode'];
    checkBorrower($bookBarcode, $userID,$conn); //check if user is allowed to report lost
    $lostStmt = $conn->prepare("UPDATE `bookItem` SET lostDate = DATE(?),onHold=1 WHERE bookBarcode =? and lostDate IS NULL");
    $todayDate = date("Y-m-d");
    $lostStmt->bind_param("si", $todayDate,$bookBarcode);
    //echo("Reporting lost book");
    $lostStmt->execute();
    echo("Book is has successfully been reported as lost");
  }
  else{
    exit(NO_BOOK_MATCHED);
  }    
} 
else{ //Not logged in
  redirectToLogin();
}

$conn->close();

function checkBorrower($bookBarcode, $borrowerID,$conn){
  $borrowStmt = $conn->prepare("SELECT * FROM transactions WHERE bookBarcode=? AND borrowedBy =? AND returnDate IS NULL"); //check if book is issued to user
  $borrowStmt->bind_param("ii", $bookBarcode,$borrowerID);

  $borrowStmt->execute();
  $result = $borrowStmt->get_result();
  if(!$result){
      exit(QUERY_FAILURE);
  }
  $row =$result->fetch_assoc();
  
  /*$borrowedBy=$row['borrowedBy'];
  echo "<br>borrowedBy -> $borrowedBy";
  $numRow =$result->num_rows;
  echo "$numRow";*/
  //if($borrowedBy !=$borrowerID){
  if($result->num_rows === 0){
    exit(UNAUTHORIZED_NOT_OWNER);
  }
 
}

?>

