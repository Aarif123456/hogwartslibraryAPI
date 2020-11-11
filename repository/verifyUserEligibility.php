<?php
//verify if user can take out book
/* Imports */
require_once 'error.php';

/* Return true if user is blacklisted  */
function checkUserBlacklisted($userID, $conn)
{
    $stmt = $conn->prepare("SELECT memberID FROM members WHERE memberID=? and status=?");
    $blacklisted = ACCOUNT_BLACKLISTED;
    $stmt->bind_param("ii", $userID, $blacklisted);
    $stmt->execute();

    return !(empty($stmt->num_rows));
}

