<?php

/* Imports */
require_once __DIR__ . '/error.php';

function insertLibrarian($librarianID, $conn, $debug = false)
{
    $stmt = $conn->prepare(
        'INSERT INTO librarianAccount (librarianID, active) VALUES (:librarianID, 1) ON DUPLICATE KEY UPDATE active = 1'
    );
    $stmt->bindValue(':librarianID', $librarianID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteLibrarian($librarianID, $conn, $debug = false)
{
    $deleteQuery = 'UPDATE librarianAccount SET active = 0 WHERE librarianID = :librarianID';
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bindValue(':librarianID', $librarianID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}

