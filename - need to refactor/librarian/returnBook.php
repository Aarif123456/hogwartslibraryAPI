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

if(isset($_POST['bookBarcode'])){
  $bookBarcode = $_POST['bookBarcode'];
}
else{
    exit("missing book");
}
if (checkSessionInfo() && validateUser($_SESSION['userID']))   
{  
    if(!(validateLibrarian($_SESSION['userID']))) {
        echo "not a librarian";
        redirectToLogin();
      }
  $librarianID = $_SESSION['userID']; 
  $username =htmlentities($_SESSION['username']);
  //echo "Welcome librarian: $username" ; 
    //return has 2 simple steps because the the back-end add in the fines when you decrement the remaining copy and then stores the fine, and decrements how many books the user has and hold is reset on check out

    try {
        $conn->autocommit(false);
        //1.Check if book has been marked as lost. If so then make lostDate null and back-end handles the rest
        $lostStmt = $conn->prepare(
            "SELECT lostDate,onHold FROM bookItem WHERE bookBarcode=?"
        ); //check if book is issued to user
        $lostStmt->bind_param("i", $bookBarcode);
        $lostStmt->execute();
        $result = $lostStmt->get_result();
    if(!$result || $result->num_rows ==0){
          exit("could not get Info about book");
    }
        $result = $result->fetch_assoc();
        $lost = $result['lostDate'];
        if (!(empty($lost))) { //book was lost
            $foundStmt = $conn->prepare(
                "UPDATE bookItem SET lostDate = NULL, onHold=1 WHERE bookBarcode =? AND lostDate IS NOT NULL"
            );
            $foundStmt->bind_param("i", $bookBarcode);
            $foundStmt->execute();
            echo "The book is now found!!!<br> ";
        }
        //echo"book not lost";
        //2.Update returnDate to current date in transaction
        $todayDate = date("Y-m-d");
        $returnStmt = $conn->prepare(
            "UPDATE transactions SET returnDate = DATE(?), returnerID=? WHERE bookBarcode =? AND returnDate IS NULL"
        );
        $returnStmt->bind_param("sii", $todayDate, $librarianID, $bookBarcode);
        $returnStmt->execute();
        $bookBarcode = htmlentities($bookBarcode);
        echo "book with barcode $bookBarcode has been returned on $todayDate!";

        $conn->autocommit(true);
    } catch (Exception $e) {
        $conn->rollback(); //remove all queries from queue if error (undo)
        echo INTERNAL_SERVER_ERROR;
    }
} else { //Not logged in
    redirectToLogin();
}

$conn->close();



