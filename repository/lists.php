<?php

/* Imports */
require_once __DIR__ . '/lists/headmasterLists.php';
require_once __DIR__ . '/lists/librarianLists.php';
require_once __DIR__ . '/lists/reservationLists.php';
require_once __DIR__ . '/error.php';

function getHeadmasterListResults($listType, $conn)
{
    // get query
    $query = headmasterListJSON($listType);
    // if no query return back null
    if (empty($query)) {
        exit(INVALID_LIST);
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query));
}

function getLibrarianListResults($listType, $conn)
{
    // get query
    $stmt = librarianListJSON($listType, $conn);
    // if no query return back null
    if (empty($stmt)) {
        exit(INVALID_LIST);
    }

    // otherwise return back result from query
    return getExecutedResult($stmt);
}

function getReservationListResults($listType, $params, $conn)
{
    // get query
    $stmt = reservationListJSON($listType, $params, $conn);
    // if no query return back null
    if (empty($stmt)) {
        exit(INVALID_LIST);
    }

    // otherwise return back result from query
    return getExecutedResult($stmt);
}

