<?php

function getLibrarianCharts($chartType)
{
    switch ($chartType) {
        case "unusedList":
            return "SELECT * FROM books WHERE bookISBN NOT IN(SELECT bookISBN FROM bookItem INNER JOIN transactions ON  transactions.bookBarcode =bookItem.bookBarcode WHERE borrowedDate >DATE_SUB(NOW(),INTERVAL 5 YEAR))";
        case "lostList":
            return "SELECT bookISBN, bookName, author, COUNT(*) as cnt FROM bookItem NATURAL JOIN books WHERE status=4 GROUP BY bookISBN ORDER BY cnt DESC";
        default:
            return "";
    }
}

