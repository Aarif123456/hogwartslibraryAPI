<?php
function getHeadmasterCharts($chartType){
    switch ($chartType) {
        /* teacher and reserved books */
        case "reservationList":
            return "SELECT memberID,fname,lname,COUNT(*) AS booksReserved FROM members INNER JOIN courses ON professorID =memberID INNER JOIN bookItem ON reservedFor = courseID AND courseID !=-1 GROUP BY professorID";
        case "fineList":
           /* list of members with fines */
           return "SELECT memberID,fname,lname,fines  FROM members WHERE fines>0 AND memberID NOT IN (SELECT headmasterID AS memberID FROM headmasters) ORDER BY fines DESC";
        case "activeList":
           /* user and teacher active and inactive */
           return "SELECT 'active: professors' AS userCategory,COUNT(*) AS cnt FROM transactions INNER JOIN professor ON borrowedBy  = professorID WHERE borrowedDate >DATE_SUB(NOW(),INTERVAL 1 YEAR) UNION SELECT 'active: students' AS userCategory,COUNT(*) AS cnt FROM transactions INNER JOIN students ON borrowedBy = studentID WHERE borrowedDate >DATE_SUB(NOW(),INTERVAL 1 YEAR) UNION SELECT 'inactive: professors' AS userCategory,COUNT(*) AS cnt FROM transactions RIGHT OUTER JOIN professor ON borrowedBy = professorID WHERE borrowedBy IS NULL OR borrowedDate <DATE_SUB(NOW(),INTERVAL 1 YEAR) UNION SELECT 'inactive: students' AS userCategory,COUNT(*) AS cnt FROM transactions RIGHT OUTER JOIN students ON borrowedBy  = studentID WHERE borrowedBy IS NULL OR borrowedDate <DATE_SUB(NOW(),INTERVAL 1 YEAR)";
        default: 
            return "";
    }
    return "";
}

?>