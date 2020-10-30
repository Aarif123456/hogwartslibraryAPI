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

if (checkSessionInfo() && validateUser($_SESSION['userID']))   
{  
    //if you are a librarian you must enter the ID of the person you are holding for
     if(validateLibrarian($_SESSION['userID'])){
        if(!(isset($_POST['userID']))){
          exit("no user is selected");
        }
        $renewerID =$_POST['userID'];
    } 
    //not a librarian but a valid user
    elseif(validateUser($_POST['userID']) && !($_SESSION['librarian'])) {
       $renewerID= $_SESSION['userID'];
    }
    elseif(!(validateUser($_POST['userID'])) && !($_SESSION['librarian'])) {
        redirectToLogin();
    }
    else{
        exit();
    }
    //renewal code
    if(isset($_POST['bookBarcode']) && !(empty($_POST['bookBarcode']))) {
      $bookBarcode = $_POST['bookBarcode'];
        checkUser($renewerID,$conn);
        checkBookReservation($bookBarcode,$conn);
      $renewedTime = checkBorrower($bookBarcode, $renewerID,$conn); //check if user is allowed to hold 
      //purgeHoldTable($conn); //clean up hold table
      if($renewedTime<2){ //Can renew  a book Maximum of 2 times
          //Renew book by updating renew in transaction
          $renewStmt = $conn->prepare("UPDATE `transactions` SET renewedTime = renewedTime +1 WHERE borrowedBy =? AND bookBarcode =? AND returnDate IS NULL");
          $renewStmt->bind_param("ii", $renewerID,$bookBarcode);
          $renewStmt->execute();
          //Back end handles finding the date and adding in fines
          echo "Book renewed!";
      }
      else{
          exit("Book has been renewed the maximum amount of times");
      }
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
   $borrowStmt = $conn->prepare("SELECT renewedTime FROM `transactions` WHERE bookBarcode=? AND borrowedBy =? AND returnDate IS NULL"); //check if book is issued to user
  $borrowStmt->bind_param("ii", $bookBarcode,$borrowerID);
  $borrowStmt->execute();
  $result = $borrowStmt->get_result();
  if(!$result){
      exit(QUERY_FAILURE);
  }
  $result = $result->fetch_assoc();
  if($result->num_rows ===0){
    exit(UNAUTHORIZED_NOT_OWNER);
  }
  return $result['renewedTime'];
}
function checkBookReservation($bookBarcode,$conn){
   $reservationStmt=$conn->prepare("SELECT lostDate,reservedFor,onHold,holds,remainingCopies FROM `bookItem` NATURAL JOIN `books` WHERE bookBarcode=?"); 
   //check if book is reserved or lost or onHold
  $reservationStmt->bind_param("i", $bookBarcode);
  $reservationStmt->execute();
  $result = $reservationStmt->get_result();
  if(!$result){
      exit("Could not get Info about reservation");
  }
  $result = $result->fetch_assoc();
  if(!(empty($result['lostDate']))){
      exit ("This book has been reported as lost please return it.");
  }
  if(!(empty( $result['reservedFor']))){
      //echo "book reserved!";
      exit (" it is reserved");
  }
  $holds=$result['holds'];
  $remainingCopies=$result['remainingCopies'];
  if((!(empty( $result['onHold'])))|| ($holds>0&&$holds>$remainingCopies)) {
    //echo "book has holds";
    exit (" it has holds");
  }
}
?>

