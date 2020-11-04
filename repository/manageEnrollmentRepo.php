<?php

/* Imports */
require_once 'error.php';

function insertEnrollment($studentID, $courseID, $conn, $debug = false)
{
    $stmt = $conn->prepare("INSERT INTO `courseEnrolled` (`courseID`, `studentID`) VALUES (?, ?)");
    $stmt->bind_param("ii", $courseID, $studentID);

    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteEnrollment($enrollmentNumber, $conn, $debug = false)
{
    $deleteStmt = "DELETE FROM courseEnrolled WHERE enrollmentNumber=?";
    $stmt = $conn->prepare($deleteStmt);
    $stmt->bind_param("i", $enrollmentNumber);

    return safeWriteQueries($stmt, $conn, $debug);
}

