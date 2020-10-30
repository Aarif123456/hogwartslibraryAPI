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
if(!(isValidPostVar('bookBarcode') && isValidPostVar('borrowedBy'))) exit(MISSING_PARAMETERS);

if (checkSessionInfo() && validateLibrarian($_SESSION['userID']))   
{
    $bookBarcode = $_POST['bookBarcode'];
    $borrowedBy = $_POST['borrowedBy'];
    $librarianID = $_SESSION['userID']; 
    $username = $_SESSION['username'];
    //echo "Welcome librarian: $username" ;  
    
} else{
    redirectToLogin();
}



if (checkSessionInfo())   
  {  
    //var_dump($_SESSION);
  if(!(validateLibrarian($_SESSION['userID']))) {
    echo "not a librarian";
    redirectToLogin();
  }

 
  //Just in case something weird happens use html entities
    
  $holdFlag = verifyBookAvailability($bookBarcode,$borrowedBy,$librarianID,$conn);
  try //prepare and insert to avoid injection
  { 
    // $time= date('Y-m-d H:i:s'); ************* use to set check out date - use separate class to handle the date time function
    $conn->autocommit(FALSE); //turn on transactions
    $transactionStmt = $conn->prepare("INSERT INTO transactions (bookBarcode, borrowedBy, issuerID) VALUES (?, ?, ?)");
    $transactionStmt->bind_param("iii", $bookBarcode, $borrowedBy , $librarianID);
    $transactionStmt->execute();
    $tranId =  $conn->insert_id;
    if(!(empty($holdFlag))) { //if their was a hold on the book update bookItem
      //remove reservation since book is checked out
      $holdStmt =$conn->prepare("UPDATE `bookItem` SET `onHold`=0  WHERE `bookBarcode`=? AND `onHold`=1 ");
      $holdStmt->bind_param("i", $bookBarcode);
      $holdStmt->execute();
    }

    $conn->autocommit(TRUE); 
    echo "Transaction successful : Book with barcode $bookBarcode has been issued to $borrowedBy by the librarianID $librarianID";
  }catch(Exception $e) {
    $conn->rollback(); //remove all queries from queue if error (undo)
    echo INTERNAL_SERVER_ERROR;
   }
} 
else{
  echo UNAUTHORIZED_NO_LOGIN;
  redirectToLogin();//Send to login page
}
$conn->close(); 

function verifyBookAvailability($bookBarcode,$borrowedBy,$librarianID,$conn){ 
//see if book can be checked out
  verifySelfCheckout($librarianID, $borrowedBy);
  checkUser($borrowedBy,$conn);
  return checkBookAvailability($bookBarcode,$borrowedBy,$conn);
}
function checkBookAvailability($bookBarcode,$borrowedBy,$conn){
  //get checkedOutFlag, lostDate, reservedFor,onHold from bookItem 
  $verifyStmt = $conn->prepare("SELECT * FROM bookItem WHERE bookBarcode=?");
  $verifyStmt->bind_param("i", $bookBarcode);
  $verifyStmt->execute();
  $result = $verifyStmt->get_result();

  if(!$result || $result->num_rows==0){
    exit("Could not find book with barcode");    
  }
  $result= $result->fetch_assoc();
  if($result['checkedOutFlag']|| !(empty($result['lostDate']))) {
    //check if book has been taken out or if it has been reported as lost
     exit ("This book cannot be signed out");     
  }
  //check reservation
  if(!(empty( $reservedFor))){
    if(!(checkReservation($reservedFor, $bookBarcode, $borrowedBy,$conn))){ 
      //if not reserved for student
      exit ("user is not eligible to take out this copy");      
    }
  }
  //check hold status
  $onHold = $result['onHold'];  
  if(!(empty($onHold))) {  //check if book is on hold for that user     
    if(!(checkHoldStatus($bookBarcode, $borrowedBy,$conn))){
      exit ("Book has holds. This user cannot sign it out right now");      
    }
  }
  return $onHold;
}
?>
