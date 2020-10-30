<?php
/* Imports */
require_once 'error.php';

/* Queries */
function getInsertQuery($courseName, $professorID, $conn){
    $insertCourse = "INSERT INTO `courses` (`courseName`, `professorID`) VALUES (?, ?)";
    $stmt = $conn->prepare($insertCourse);
    $stmt->bind_param("si", $courseName, $professorID);
    return $stmt;
}

function getInsertQueryWithTerm($courseName, $professorID, $termOffered, $conn){
    $insertCourseWithTerm = "INSERT INTO `courses` (`courseName`, `professorID`, `TermOffered`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertCourseWithTerm);
    $stmt->bind_param("sis", $courseName, $professorID, $termOffered);
    return $stmt;
}

function insertCourse($courseName, $professorID, $termOffered, $conn, $debug=false){
    /* If we have know the term then set it */
    $stmt = empty($termOffered) ? getInsertQuery($courseName, $professorID, $conn) 
                                : getInsertQueryWithTerm($courseName, $professorID, $termOffered, $conn);
    if(empty($stmt)) return false;
    return safeWriteQueries($stmt, $conn, $debug);
}

function deleteCourse($courseID, $conn, $debug=false){
    $deleteCourse = "DELETE FROM courses WHERE courseID=?";
    $stmt = $conn->prepare($deleteCourse);
    $stmt->bind_param("i", $courseID);
    return safeWriteQueries($stmt, $conn, $debug);
}
?>