<?php

/* Imports */
require_once __DIR__ . '/error.php';

/* Queries */
function getInsertQuery($courseName, $professorID, $conn)
{
    $insertCourse = 'INSERT INTO `courses` (`courseName`, `professorID`) VALUES (:courseName, :professorID)';
    $stmt = $conn->prepare($insertCourse);
    $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
    $stmt->bindValue(':professorID', $professorID, PDO::PARAM_INT);

    return $stmt;
}

function getInsertQueryWithTerm($courseName, $professorID, $termOffered, $conn)
{
    $insertCourseWithTerm = 'INSERT INTO `courses` (`courseName`, `professorID`, `TermOffered`) VALUES (:courseName, :professorID, :termOffered)';
    $stmt = $conn->prepare($insertCourseWithTerm);
    $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
    $stmt->bindValue(':professorID', $professorID, PDO::PARAM_INT);
    $stmt->bindValue(':termOffered', $termOffered, PDO::PARAM_STR);

    return $stmt;
}

function insertCourse($courseName, $professorID, $termOffered, $conn, $debug = false)
{
    /* If we have know the term then set it */
    $stmt = empty($termOffered) ? getInsertQuery($courseName, $professorID, $conn)
        : getInsertQueryWithTerm($courseName, $professorID, $termOffered, $conn);
    if (empty($stmt)) {
        return false;
    }

    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteCourse($courseID, $conn, $debug = false)
{
    $deleteCourse = 'DELETE FROM courses WHERE courseID = :courseID';
    $stmt = $conn->prepare($deleteCourse);
    $stmt->bindValue(':courseID', $courseID, PDO::PARAM_INT);

    return safeWriteQueries($stmt, $conn, $debug);
}

