<?php
/* Imports */
require_once 'error.php';

function queryUsername($username, $conn){
    // get query
    $stmt = $conn->prepare("SELECT userName FROM userAccount WHERE userName = ?");
    $stmt->bind_param("s", $username);
    // if no query return back null
    if(empty($stmt)) return null;
    // otherwise return back result from query
    return getExecutedResult($stmt);
}


?>