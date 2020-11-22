<?php

function getProfessorCharts($chartType)
{
    switch ($chartType) {
        case 'activityPerMajorAndDepartment':
            return 'SELECT major,COUNT(*) as activeUsers FROM transactions INNER JOIN members ON memberID = borrowedBy INNER JOIN students ON studentID = memberID  WHERE borrowedDate >DATE_SUB(NOW(),INTERVAL 1 YEAR) GROUP BY major UNION SELECT department as major ,COUNT(*) as activeUsers FROM transactions INNER JOIN members ON memberID = borrowedBy INNER JOIN professor ON  professorID = memberID WHERE borrowedDate >DATE_SUB(NOW(),INTERVAL 1 YEAR) GROUP BY major ORDER BY activeUsers DESC';
        default:
            return '';
    }
}

