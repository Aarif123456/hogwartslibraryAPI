<?php //verify if user can take out book
/* Imports */
require_once 'error.php';

/* For checkout, check blacklisted and if they have too many books for their user type*/
function checkUserBlacklisted($userID, $conn){
    $stmt = $conn->prepare("SELECT memberID FROM members WHERE memberID=? and status=?");
    $stmt->bind_param("ii", $userID, ACCOUNT_BLACKLISTED);
    return !(empty(getExecutedResult($stmt)));
}

?>