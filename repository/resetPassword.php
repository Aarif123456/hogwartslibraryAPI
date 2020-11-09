<?php

/* Imports */
require_once 'error.php';

function resetQuery($uID, $password, $conn, $debug = false)
{
    $stmt = $conn->prepare("UPDATE userAccount SET `password` = ? WHERE userID = ?");
    $password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param("si", $password, $uID);

    return safeWriteQueries($stmt, $conn, $debug);
}

