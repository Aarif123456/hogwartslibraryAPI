<?php

/* Imports */
require_once __DIR__ . '/error.php';

function insertEnrollment($studentID, $courseID, $conn, $debug = false)
{
    $stmt = $conn->prepare('INSERT INTO `courseEnrolled` (`courseID`, `studentID`) VALUES (:courseID, :studentID)');
    $stmt->bindValue(':courseID', $courseID, PDO::PARAM_INT);
    $stmt->bindValue(':studentID', $studentID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteEnrollment($enrollmentNumber, $conn, $debug = false)
{
    $deleteQuery = 'DELETE FROM courseEnrolled WHERE enrollmentNumber = :enrollmentNumber';
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bindValue(':enrollmentNumber', $enrollmentNumber, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}

