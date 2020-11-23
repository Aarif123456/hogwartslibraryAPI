<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';

/* User is eligible for check out if they meet the following criteria 
    1. They have an active account
    2. Are one of the following
        a. a student with less than 10 books checked out 
        b. professor with less than 50 books checked out 
        c. headmaster
*/
function checkUserEligibleForCheckout($userID, $conn)
{
    $query = '  SELECT 
                    memberID 
                FROM 
                    members
                WHERE 
                    status = :accountActive
                    AND memberID IN 
                                    (
                                        SELECT 
                                            memberID 
                                        FROM 
                                            members 
                                            INNER JOIN 
                                                students ON memberID = studentID 
                                        WHERE 
                                            memberID = :studentID 
                                            AND numBooks < :studentBookLimit
                                        UNION
                                        SELECT 
                                            memberID 
                                        FROM 
                                            members 
                                            INNER JOIN 
                                                professor ON memberID = professorID 
                                        WHERE 
                                            memberID = :professorID
                                            AND numBooks < :professorBookLimit
                                        UNION
                                        SELECT 
                                            memberID 
                                        FROM 
                                            members INNER JOIN headmasters ON memberID = headmasterID
                                        WHERE 
                                            memberID = :headmasterID
                                    )
            ';

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':accountActive', ACCOUNT_ACTIVE, PDO::PARAM_INT);
    $stmt->bindValue(':studentBookLimit', STUDENT_BOOK_LIMIT, PDO::PARAM_INT);
    $stmt->bindValue(':professorBookLimit', PROFESSOR_BOOK_LIMIT, PDO::PARAM_INT);
    $stmt->bindValue(':studentID', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':professorID', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':headmasterID', $userID, PDO::PARAM_INT);

    return getExecutedResult($stmt);
}
