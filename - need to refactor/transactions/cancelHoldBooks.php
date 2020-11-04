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
require_once 'repository/verifyUserEligibility.php';
require_once 'repository/database.php';

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser()) {
    //if you are a librarian you must enter the ID of the person you are holding for
    if (validateLibrarian()) {
        if (!(isset($_POST['userID']))) {
            exit("no user is selected");
        }
        $holderID = $_POST['userID'];
        //echo "Am librarian holding on behalf of ";
    } //not a librarian but a valid user
    elseif (validateUser()) {
        $holderID = $_SESSION['userID'];
        //echo "Not a librarian. I am holding ";
    } else {
        //echo "I am no one ...";
        exit();
    }
    //echo "$holderID";
    if (isValidPostVar('holdID')) {
        //check if user is allowed to hold 
        $holdID = $_POST['holdID'];
        purgeHoldTable($conn); //clean up hold table
        $holdStmt = $conn->prepare("SELECT * FROM holds WHERE holdID =?");
        $holdStmt->bind_param("i", $holdID);
        $holdStmt->execute();
        $holdResult = $holdStmt->get_result();
        if (!$holdResult || $holdResult->num_rows == 0) {
            exit("Hold does not exist");
        }
        $holdResult = $holdResult->fetch_assoc();
        //if hold has reserved copy set reserve copy to null and back-end will delete
        if (!(empty($holdResult['reservedCopy']))) {
            $holdReserveStmt = $conn->prepare("UPDATE bookItem SET onHold = 0 WHERE bookBarcode =?");
            $holdReserveStmt->bind_param("i", $holdResult['reservedCopy']);
            $holdReserveStmt->execute();
            echo "Hold cancelled. You may no longer pick up book from library";
        } else {//If no reserved copy delete hold manually and decrement queue number for everyone with same bookISBN
            $holdDeleteStmt = $conn->prepare("DELETE FROM holds WHERE holdID=?");
            $holdDeleteStmt->bind_param("i", $holdID);
            $holdDeleteStmt->execute();

            $holdUpdateStmt = $conn->prepare(
                "UPDATE holds SET queueNumber = queueNumber -1 WHERE bookISBN =? AND queueNumber > ?"
            );
            $holdUpdateStmt->bind_param("si", $holdResult['bookISBN'], $holdResult['queueNumber']);
            $holdUpdateStmt->execute();
            echo "Hold cancelled. You are no longer in queue";
        }
    } else {
        exit("no hold was selected");
    }
} else { //Not logged in
    redirectToLogin();
}

$conn->close();


