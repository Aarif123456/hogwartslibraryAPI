<?php
/* Imports */
require_once 'error.php';

function queryCatalog($searchType, $term, $conn){
    // get query
    $query = getQueryForCatalog($searchType);
    // if no query return back null
    if(empty($query)) return null;
    /* Prepare statement */
    $stmt = $conn->prepare($query);
    bindSearchQuery($searchType, $term, $stmt);
    // otherwise return back result from query
    return getExecutedResult($stmt);
}

function bindSearchQuery($searchType, $term, $stmt){
    if(strcmp($searchType,'keyword')==0){ 
        $stmt->bind_param("ssssss",$term,$term,$term,$term,$term,$term);
    }
    else{
        $stmt->bind_param("s", $term);
    }
}

/* check if the search method is valid */
function validSearchMethod($searchType){
    switch ($userType){
            case "keyword":       // INTENTIONAL FALLTHROUGH
            case "title":         // INTENTIONAL FALLTHROUGH
            case "author":        // INTENTIONAL FALLTHROUGH
            case "ISBN":          // INTENTIONAL FALLTHROUGH
            case "tag":           // INTENTIONAL FALLTHROUGH
                return true;
            default:
                return false;  
        }  
}

/* get the actual query */
function getQueryForCatalog($searchType){
    // common start of search query
    $query = "SELECT bookISBN, bookName, author, pages, edition, express, category, holds FROM `books` WHERE "; 
    
    switch ($searchType){
        case "keyword":
            //separate keyword because it uses multiple parameter
            $query .= "bookISBN like CONCAT('%',?,'%') OR ";
            $query .= "bookName like CONCAT('%',?,'%') OR ";
            $query .= "author like CONCAT('%',?,'%') OR ";
            $query .= "pages like CONCAT('%',?,'%') OR ";
            $query .= "edition like CONCAT('%',?,'%') OR ";
            $query .= "category like CONCAT('%',?,'%')";
            break;
        case "title":
            $query .= "bookName like CONCAT('%',?,'%')";
            break;
        case "author":
            $query .= "author like CONCAT('%',?,'%')"; 
            break;
        case "ISBN":
            $query .= "bookISBN like CONCAT('%',?,'%')"; 
            break;
        case "tag":
             $query .= "bookISBN IN (SELECT `bookISBN` FROM bookTags WHERE tag LIKE CONCAT('%',?,'%'))";
            break;
        default:
            return "";  
    }
    return $query;    
}

?>