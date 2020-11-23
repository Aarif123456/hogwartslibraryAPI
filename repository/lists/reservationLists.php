<?php

function reservationListJSON($listType, $params, $conn)
{
    $stmt = null;
    switch ($listType) {
        case 'loadReservedBooks':
            $stmt = $conn->prepare(
                'SELECT `bookISBN`,`bookName`,`author`,`totalCopies`FROM bookItem NATURAL JOIN books WHERE `reservedFor`=:courseID'
            );
            $stmt->bindValue(':courseID', $params->courseID, PDO::PARAM_INT);

            return $stmt;
        case 'loadCoursesStudent':
            $stmt = $conn->prepare(
                'SELECT `courseID`, `courseName` FROM `courseEnrolled` NATURAL JOIN `courses` WHERE `studentID` = :userID'
            );
            $stmt->bindValue(':userID', $params->userID, PDO::PARAM_INT);

            return $stmt;
        case 'loadCoursesProfessor':
            $stmt = $conn->prepare(
                'SELECT `courseID`,`courseName`FROM `courses` WHERE `professorID` = :userID'
            );
            $stmt->bindValue(':userID', $params->userID, PDO::PARAM_INT);

            return $stmt;

        case 'loadAvailableBooks':
            $stmt = $conn->prepare(
                'SELECT `bookISBN`,`bookName`,`author`FROM `books` WHERE `totalCopies`>0'
            );

            return $stmt;

        case 'loadProfessor':
            $stmt = $conn->prepare(
                'SELECT `professorID`,`fname`,`lname` FROM `professor` INNER JOIN `members` ON professorID= memberID WHERE professorID>0 ORDER BY professorID'
            );

            return $stmt;

        default:
    }

    return null;
}


