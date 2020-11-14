<?php

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function insertReservation($courseID, $bookISBN, $professorID, $numCopies, $conn, $debug = false): int
{
    $query = "
            UPDATE 
                bookItem 
            SET 
                reservedFor = ? 
            WHERE 
                reservedFor IS NULL 
                AND bookISBN = ? 
                AND ? IN 
                    (
                        SELECT 
                            courseID
                        FROM 
                            courses 
                        WHERE 
                            courseID = ? 
                            AND professorID = ?
                    )
                LIMIT ?
            ";
    $reserveStmt = $conn->prepare($query);
    $numCopies = min(MAXIMUM_COPIES_RESERVED, max($numCopies, 1));
    $reserveStmt->bind_param(
        "isiiii",
        $courseID,
        $bookISBN,
        $courseID,
        $courseID,
        $professorID,
        $numCopies
    );

    return safeUpdateQueries($reserveStmt, $conn, $debug);
}

function deleteReservation($professorID, $courseID, $bookISBN, $conn, $debug = false): bool
{
    $query = "
                UPDATE 
                    bookItem 
                    INNER JOIN courses ON courseID = reservedFor
                SET 
                    reservedFor = NULL 
                WHERE 
                    bookISBN = ? 
                    AND courseID = ? 
                    AND professorID = ?
            ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sii",
        $bookISBN,
        $courseID,
        $professorID
    );

    return safeWriteQueries($stmt, $conn, $debug);
}