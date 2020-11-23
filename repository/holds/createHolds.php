<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';

function createHold($bookISBN, $holderID, $conn, $debug = false): bool
{
    $stmt = $conn->prepare(
        'INSERT INTO holds (bookISBN, holderID, holdExpiryDate) VALUES (:bookISBN, :id, DATE_ADD(CURDATE(), INTERVAL 1 YEAR))'
    );
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);
    $stmt->bindValue(':id', $holderID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug) && updateBooktable($bookISBN, $conn, $debug);
}

function updateBookTable($bookISBN, $conn, $debug = false): bool
{
    $stmt = $conn->prepare(
        'UPDATE books SET holds = holds+1 WHERE bookISBN = :bookISBN'
    );
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);

    return safeWriteQueries($stmt, $conn, $debug);
}

