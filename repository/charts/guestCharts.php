<?php

function getGuestCharts($chartType)
{
    switch ($chartType) {
        case "category":
            return "SELECT category,COUNT(*) as cnt FROM transactions NATURAL JOIN bookItem NATURAL JOIN books GROUP BY category ORDER BY cnt DESC";
        case "major":
            return "SELECT major,COUNT(*) as cnt FROM transactions INNER JOIN students ON borrowedBy = studentID GROUP BY major ORDER BY cnt DESC";
        case "allTime":
            return "SELECT bookISBN,bookName,COUNT(*) as cnt FROM transactions NATURAL JOIN bookItem NATURAL JOIN books GROUP BY bookISBN ORDER BY cnt DESC";
        case "houseFine":
            return "SELECT house,SUM(fines) as total FROM members INNER JOIN students ON studentID = memberID GROUP BY house ORDER BY total DESC";
        default:
            return "";
    }

}

