<?php

/* Imports */
require_once __DIR__ . '/error.php';

function queryCatalog($searchType, $term, $conn)
{
    // get query
    $query = getQueryForCatalog($searchType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }
    /* Prepare statement */
    $stmt = $conn->prepare($query);
    bindSearchQuery($searchType, $term, $stmt);

    // otherwise return back result from query
    return getExecutedResult($stmt);
}

function bindSearchQuery($searchType, $term, $stmt)
{
    if (strcmp($searchType, 'keyword') == 0) {
        $stmt->bindValue(':bookISBN', $term, PDO::PARAM_STR);
        $stmt->bindValue(':bookName', $term, PDO::PARAM_STR);
        $stmt->bindValue(':author', $term, PDO::PARAM_STR);
        $stmt->bindValue(':pages', $term, PDO::PARAM_STR);
        $stmt->bindValue(':edition', $term, PDO::PARAM_STR);
        $stmt->bindValue(':category', $term, PDO::PARAM_STR);
    } else {
        $stmt->bindValue(':term', $term, PDO::PARAM_STR);
    }
}

/* check if the search method is valid */
function validSearchMethod($searchType)
{
    switch ($searchType) {
        case 'keyword':       // INTENTIONAL FALLTHROUGH
        case 'title':         // INTENTIONAL FALLTHROUGH
        case 'author':        // INTENTIONAL FALLTHROUGH
        case 'ISBN':          // INTENTIONAL FALLTHROUGH
        case 'tag':           // INTENTIONAL FALLTHROUGH
            return true;
        default:
            return false;
    }
}

/* get the actual query */
function getQueryForCatalog($searchType)
{
    // common start of search query
    $query = 'SELECT bookISBN, bookName, author, pages, edition, express, category, holds FROM `books` WHERE ';

    switch ($searchType) {
        case 'keyword':
            //separate keyword because it uses multiple parameter
            $query .= 'bookISBN like CONCAT(\'%\',:bookISBN,\'%\') OR ';
            $query .= 'bookName like CONCAT(\'%\',:bookName,\'%\') OR ';
            $query .= 'author like CONCAT(\'%\',:author,\'%\') OR ';
            $query .= 'pages like CONCAT(\'%\',:pages,\'%\') OR ';
            $query .= 'edition like CONCAT(\'%\',:edition,\'%\') OR ';
            $query .= 'category like CONCAT(\'%\',:category,\'%\')';
            break;
        case 'title':
            $query .= 'bookName like CONCAT(\'%\',:term,\'%\')';
            break;
        case 'author':
            $query .= 'author like CONCAT(\'%\',:term,\'%\')';
            break;
        case 'ISBN':
            $query .= 'bookISBN like CONCAT(\'%\',:term,\'%\')';
            break;
        case 'tag':
            $query .= 'bookISBN IN (SELECT `bookISBN` FROM bookTags WHERE tag LIKE CONCAT(\'%\',:term,\'%\'))';
            break;
        default:
            return '';
    }

    return $query;
}

