<?php //verify if user can take out book

/* Used ONLY during check out*/
function checkHoldStatus( $bookBarcode, $borrowedBy,$conn){
  $holdStmt = $conn->prepare("SELECT holderID FROM holds WHERE reservedCopy =? AND queueNumber = 0"); //queue number 0 is on pick up
  $holdStmt->bind_param("i", $bookBarcode);
  $holdStmt->execute();
  $holdRslt = $holdStmt->get_result();
  $holdRslt = $holdRslt->fetch_assoc();
  return $borrowedBy == $holdRslt['holderID'];
}
/* Used ONLY during check out*/
function checkReservation($reservedFor, $bookBarcode, $borrowedBy,$conn){ 
  //if reserved check if student is in class
  $reservationStmt = $conn->prepare("SELECT * FROM courseEnrolled WHERE courseID =? AND studentID=?");
  $reservationStmt->bind_param("ii", $reservedFor,$borrowedBy);
  $reservationStmt->execute();
  $reservationRslt = $reservationStmt->get_result();
  if($reservationRslt->num_rows ==0){
    return false;
  }
  return true;
}

/* ONLY used in holdBook */
function checkHold($bookISBN,$holderID,$conn){
  $holdStmt = $conn->prepare("SELECT * FROM holds WHERE bookISBN =? AND holderID =?");
  $holdStmt->bind_param("si", $bookISBN,$holderID);
  $holdStmt->execute();
  $holdRslt = $holdStmt->get_result();
  if($holdRslt->num_rows !=0){
    exit("User already has an active hold on the book");
  }
}
/* Used in hold book and cancel hold book - remove because we are know using timestamps */
/* Currently we will manually skip the hold if it is expired - in the future mark it is expired
if pass the expiry date -possibly a cron job */
function purgeHoldTable($conn){
  $todayDate = date("Y-m-d");
  $purgeStmt = $conn->query("SELECT * FROM holds WHERE holdExpiryDate < DATE('".$todayDate."') and reservedCopy IS NOT NULL"); //get all expired holds that the user was suppose to pick up
  while($row = $purgeStmt->fetch_assoc()) { //remove them all by un-setting them
    $purge2Stmt = $conn->prepare("UPDATE bookItem SET onHold =0 WHERE onHold = 1 AND bookBarcode = ? ");
    $purge2Stmt->bind_param("i", $row['reservedCopy']);
    $purge2Stmt->execute();
  }
  //delete any other expired holds 
  $purge1Stmt = $conn->prepare("DELETE FROM holds WHERE holdExpiryDate < DATE(?)");
  $purge1Stmt->bind_param("s", $todayDate);
  $purge1Stmt->execute();
}


?>
