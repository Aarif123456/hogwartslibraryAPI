<?php

/* Imports */
require_once 'lists/headmasterLists.php';
require_once 'lists/librarianLists.php';
require_once 'error.php';

function getHeadmasterListResults($listType, $conn)
{
    // get query
    $query = headmasterListJSON($listType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return $conn->query($query);
}

function getLibrarianListResults($listType, $conn)
{
    // get query
    $stmt = librarianListJSON($listType, $conn);
    // if no query return back null
    if (empty($stmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($stmt);
}

