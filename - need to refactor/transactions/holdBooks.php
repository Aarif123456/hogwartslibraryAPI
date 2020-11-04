<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/verifyUserEligibility.php';
require_once '../repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

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
    } elseif (!(validateUser())) {
        redirectToLogin();
        //echo "I am neither a librarian or a user";
    } else {
        //echo "I am no one ...";
        exit();
    }
    //echo "$holderID";
    if (isValidPostVar('bookISBN')) {
        if (checkUserBlacklisted($holderID, $conn)) {
            exit(USER_BLACKLISTED);
        }
        $bookISBN = $_POST['bookISBN'];
        purgeHoldTable($conn); //clean up hold table
        checkHold($bookISBN, $holderID, $conn); //make user does not have a previous hold on it

        //if there are remaining copies of the book find one copy and place it on hold
        if (isset($_POST['courseID'])) {
            // If we remove reserved for from bookItem table
            // SELECT  COUNT(*) as cnt,bookBarcode FROM bookItem NATURAL JOIN reservations WHERE bookISBN = ? AND lostDate IS NULL AND onHold =0 AND checkedOutFlag = 0 AND courseID =?  LIMIT 1
            $copyStmt = $conn->prepare(
                "SELECT  COUNT(*) as cnt,bookBarcode FROM `bookItem` WHERE bookISBN = ? AND lostDate IS NULL AND onHold =0 AND checkedOutFlag = 0 AND reservedFor =? LIMIT 1"
            );
            $copyStmt->bind_param("si", $bookISBN, $_POST['courseID']);
        } else {
            // SELECT  COUNT(*) as cnt,bookBarcode FROM bookItem WHERE bookISBN = ? AND lostDate IS NULL AND onHold =0 AND checkedOutFlag = 0 AND bookBarcode NOT IN (SELECT bookBarcode FROM reservations WHERE bookISBN = ?) LIMIT 1
            $copyStmt = $conn->prepare(
                "SELECT  COUNT(*) as cnt,bookBarcode FROM `bookItem `WHERE bookISBN = ? AND lostDate IS NULL AND onHold =0 AND checkedOutFlag = 0 AND reservedFor IS NULL LIMIT 1"
            );
            $copyStmt->bind_param("s", $bookISBN);
        }
        $copyStmt->execute();
        $result = $copyStmt->get_result();

        if (!$result) {
            exit ("unable to get info on books");
        }
        $result = $result->fetch_assoc();

        if ($result['cnt'] > 0) { //if book is available
            $reservedCopy = $result['bookBarcode'];
            $holdStmt = $conn->prepare("INSERT INTO holds (bookISBN, holderID, reservedCopy) VALUES (?, ?, ?)");
            $holdStmt->bind_param("sii", $bookISBN, $holderID, $reservedCopy);
        } else {//if all copies are out then create you become put in a queue handled by the SQL server
            $holdStmt = $conn->prepare("INSERT INTO holds (bookISBN, holderID) VALUES (?, ?)");
            $holdStmt->bind_param("si", $bookISBN, $holderID);
        }
        try { //execute statements and catch error
            $conn->autocommit(false); //turn on transactions
            $holdStmt->execute();
            $holdID = $conn->insert_id;
            if (!(isset($reservedCopy))) { //If you have to wait in a queue then look at queue
                $queueQuery = $conn->query("SELECT queueNumber FROM `holds` WHERE holdID = " . $holdID);
                if (!$queueQuery) {
                    exit("Unable to get queue number");
                }
                $queueQuery = $queueQuery->fetch_assoc();
                $queueNumber = $queueQuery['queueNumber'];
                echo "Hold queue number is $queueNumber";
            } else {//If you have a book reserved
                $queueQuery = $conn->query("UPDATE `bookItem` SET onHold = 1 WHERE bookBarcode = " . $reservedCopy);
                echo "book with barcode $reservedCopy is reserved for you at the library. Please pick it within a month";
            }
            $conn->autocommit(true);
        } catch (Exception $e) {
            $conn->rollback(); //remove all queries from queue if error (undo)
            echo INTERNAL_SERVER_ERROR;
        }
    } else {
        exit(NO_BOOK_MATCHED);
    }
} else { //Not logged in
    echo UNAUTHORIZED_NO_LOGIN;
}

$conn->close();
