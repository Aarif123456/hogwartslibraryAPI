<?php
function getStudentCharts($chartType){
    switch ($chartType) {
        case "numBooksPerCategory":
            return "SELECT category,COUNT(*) as cnt FROM books GROUP BY category ORDER BY cnt DESC";
        default: 
            return "";    
    }
    return "";
}
?>