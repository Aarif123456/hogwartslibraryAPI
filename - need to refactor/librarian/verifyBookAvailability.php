<?php
//verify if user can take out book

/* Used in hold book and cancel hold book - remove because we are know using timestamps */
/* Currently we will manually skip the hold if it is expired - in the future mark it is expired
if pass the expiry date -possibly a cron job */
function purgeHoldTable($conn)
{
    $todayDate = date("Y-m-d");
    $purgeStmt = $conn->query(
        "SELECT * FROM holds WHERE holdExpiryDate < DATE('" . $todayDate . "') and reservedCopy IS NOT NULL"
    ); //get all expired holds that the user was suppose to pick up
    while ($row = $purgeStmt->fetch_assoc()) { //remove them all by un-setting them
        $purge2Stmt = $conn->prepare("UPDATE bookItem SET onHold =0 WHERE onHold = 1 AND bookBarcode = ? ");
        $purge2Stmt->bind_param("i", $row['reservedCopy']);
        $purge2Stmt->execute();
    }
    //delete any other expired holds
    $purge1Stmt = $conn->prepare("DELETE FROM holds WHERE holdExpiryDate < DATE(?)");
    $purge1Stmt->bind_param("s", $todayDate);
    $purge1Stmt->execute();
}



