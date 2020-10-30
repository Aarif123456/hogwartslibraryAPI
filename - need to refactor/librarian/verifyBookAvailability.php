<?php //verify if user can take out book

function checkUser($borrowedBy,$conn){
  $limitCheckStmt = $conn->prepare("SELECT numBooks,blacklisted,'student' as userType FROM members INNER JOIN students ON memberID=studentID WHERE memberID=? UNION SELECT numBooks,blacklisted,'professor' as userType FROM members INNER JOIN professor ON memberID=professorID WHERE memberID=? UNION SELECT numBooks,blacklisted,'headmaster' as userType FROM members INNER JOIN headmasters ON memberID=headmasterID WHERE memberID=?");
  $limitCheckStmt->bind_param("iii", $borrowedBy, $borrowedBy, $borrowedBy);
  $limitCheckStmt->execute();
  $limitResult = $limitCheckStmt->get_result();
  if(!$limitResult){
    exit("Could not get info on user");
  }
  $limitResult= $limitResult->fetch_assoc();
  $userType = $limitResult['userType'];
  if(!($limitResult['blacklisted'])==1){ //check if user has been blackListed
    if(($limitResult['numBooks']>10 && strcmp($userType,'student')==0) || ($limitResult['numBooks']>50 && strcmp($userType,'professor')==0)){
      exit("User has reached their limit. They need to return some books!");
    }
  }
  else{
    exit("user is blacklisted");
  }
}
function checkHold($bookISBN,$holderID,$conn){
  $holdStmt = $conn->prepare("SELECT * FROM holds WHERE bookISBN =? AND holderID =?");
  $holdStmt->bind_param("si", $bookISBN,$holderID);
  $holdStmt->execute();
  $holdRslt = $holdStmt->get_result();
  if($holdRslt->num_rows !=0){
    exit("User already has an active hold on the book");
  }
}
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

function checkHoldStatus( $bookBarcode, $borrowedBy,$conn){
  $holdStmt = $conn->prepare("SELECT holderID FROM holds WHERE reservedCopy =? AND queueNumber = 0"); //queue number 0 is on pick up
  $holdStmt->bind_param("i", $bookBarcode);
  $holdStmt->execute();
  $holdRslt = $holdStmt->get_result();
  $holdRslt = $holdRslt->fetch_assoc();
  return $borrowedBy == $holdRslt['holderID'];
}
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

?>
