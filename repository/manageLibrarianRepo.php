<?php
/* Imports */
require_once 'error.php';

function insertLibrarian($userID, $conn, $debug=false){
    $stmt = $conn->prepare("INSERT INTO librarianAccount (librarianID, active) VALUES (?, 1) ON DUPLICATE KEY UPDATE active = 1");
    $stmt->bind_param("i", $userID);
    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteLibrarian($userID, $conn, $debug=false){
    $deleteStmt = "UPDATE librarianAccount SET active = 0 WHERE librarianID =?";
    $stmt = $conn->prepare($deleteStmt);
    $stmt->bind_param("i", $userID);
    return safeWriteQueries($stmt, $conn, $debug);
}
?>