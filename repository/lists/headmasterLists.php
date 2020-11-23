<?php

function headmasterListJSON($listType)
{
    switch ($listType) {
        case 'loadPotentialLibrarian':
            return 'SELECT memberID, fname, lname FROM members WHERE memberID NOT IN (Select `librarianID` as `memberID` FROM librarianAccount)';
        case 'loadLibrarian':
            return 'SELECT memberID, fname, lname FROM members INNER JOIN librarianAccount ON `memberID` = `librarianID`';
        case 'loadCourses':
            return 'SELECT * FROM courses WHERE courseID >0';
        case 'loadEnrollment':
            return 'SELECT enrollmentNumber as enrollmentID, courseEnrolled.courseID, courseName, studentID, fname,lname FROM courseEnrolled LEFT OUTER JOIN courses ON courseEnrolled.courseID=courses.courseID LEFT OUTER JOIN members ON studentID = memberID';
        case 'loadStudents':
            return 'SELECT studentID,fname,lname FROM students INNER JOIN members ON studentID=memberID ORDER BY studentID';
        case 'loadProfessor':
            return 'SELECT professorID,fname,lname FROM professor INNER JOIN members ON professorID= memberID ORDER BY professorID';
        case 'loadMember':
            return 'SELECT memberID,fname,lname FROM members WHERE memberID NOT IN (Select `headmasterID` as `memberID` FROM headmasters) ORDER BY memberID';
        case 'loadUsers':
            return 'SELECT id,email FROM phpauth_users WHERE id NOT IN (Select `headmasterID` as `id` FROM headmasters) ORDER BY id';
        default:
            return '';
    }
}

