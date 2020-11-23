<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';

function insertReservation($courseID, $bookISBN, $professorID, $numCopies, $conn, $debug = false): int
{
    $query = '
            UPDATE 
                bookItem 
            SET 
                reservedFor = :reservedFor
            WHERE 
                reservedFor IS NULL 
                AND bookISBN = :bookISBN
                AND :courseIDKey IN 
                    (
                        SELECT 
                            courseID
                        FROM 
                            courses 
                        WHERE 
                            courseID = :courseID
                            AND professorID = :professorID
                    )
                LIMIT :numCopies
            ';
    $stmt = $conn->prepare($query);
    $numCopies = min(MAXIMUM_COPIES_RESERVED, max($numCopies, 1));
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);
    $stmt->bindValue(':courseIDKey', $courseID, PDO::PARAM_INT);
    $stmt->bindValue(':reservedFor', $courseID, PDO::PARAM_INT);
    $stmt->bindValue(':courseID', $courseID, PDO::PARAM_INT);
    $stmt->bindValue(':professorID', $professorID, PDO::PARAM_INT);
    $stmt->bindValue(':numCopies', (int)$numCopies, PDO::PARAM_INT);

    return safeUpdateQueries($stmt, $conn, $debug);
}

function deleteReservation($professorID, $courseID, $bookISBN, $conn, $debug = false): bool
{
    $query = '
                UPDATE 
                    bookItem 
                    INNER JOIN courses ON courseID = reservedFor
                SET 
                    reservedFor = NULL 
                WHERE 
                    bookISBN = :bookISBN
                    AND courseID = :courseID
                    AND professorID = :professorID
            ';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);
    $stmt->bindValue(':courseID', $courseID, PDO::PARAM_INT);
    $stmt->bindValue(':professorID', $professorID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}