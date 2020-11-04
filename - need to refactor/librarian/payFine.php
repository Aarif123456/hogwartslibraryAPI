<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once 'verifyBookAvailability.php';
require_once '../repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (isValidPostVar('userID') && isValidPostVar('pay')) { //need userID to 
    $userID = $_POST['userID'];
    $pay = $_POST['pay'];
} else {
    exit("missing value");
}
if (checkSessionInfo() && validateUser()) {
    if (!(validateLibrarian())) {
        echo "not a librarian";
        redirectToLogin();
    }
    $librarianID = $_SESSION['userID'];
    verifySelfCheckout($librarianID, $userID); //librarian cannot pay off their own fine
    $username = htmlentities($_SESSION['username']);
    //echo "Welcome librarian: $username";
    try {
        $conn->autocommit(false);
        $getFineStmt = $conn->prepare("SELECT fines FROM members WHERE `memberID`=?"); //get user current fine
        $getFineStmt->bind_param("i", $userID);
        $getFineStmt->execute();
        $result = $getFineStmt->get_result();
        if (!$result || $result->num_rows == 0) {
            exit("could not get info about user");
        }
        $result = $result->fetch_assoc();
        $fines = $result['fines'];
        if ($fines < $pay) { //
            $change = $pay - $fines;
            echo "$change";
        } else {
            echo "0";
        }
        //pay fine by updating member fine
        $fineStmt = $conn->prepare("UPDATE members SET fines = fines - ? WHERE `memberID` =? ");
        $fineStmt->bind_param("di", $pay, $userID);
        $fineStmt->execute();
        //echo "The fine is paid";
        $conn->autocommit(true);
    } catch (Exception $e) {
        $conn->rollback(); //remove all queries from queue if error (undo)
        echo INTERNAL_SERVER_ERROR;
    }
} else { //Not logged in
    redirectToLogin();
}

$conn->close();



