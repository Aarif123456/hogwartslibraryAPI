<?php

/* Imports */
require_once __DIR__ . '/charts/headmasterCharts.php';
require_once __DIR__ . '/charts/librarianCharts.php';
require_once __DIR__ . '/charts/professorCharts.php';
require_once __DIR__ . '/charts/studentCharts.php';
require_once __DIR__ . '/charts/guestCharts.php';
require_once __DIR__ . '/error.php';

function getHeadmasterChartsResults($chartType, $conn)
{
    // get query
    $query = getHeadmasterCharts($chartType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query)) ?: true;
}

function getLibrarianChartsResults($chartType, $conn)
{
    // get query
    $query = getLibrarianCharts($chartType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query)) ?: true;
}

function getProfessorChartsResults($chartType, $conn)
{
    // get query
    $query = getProfessorCharts($chartType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query)) ?: true;
}

function getStudentChartsResults($chartType, $conn)
{
    // get query
    $query = getStudentCharts($chartType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query)) ?: true;
}

function getGuestChartsResults($chartType, $conn)
{
    // get query
    $query = getGuestCharts($chartType);
    // if no query return back null
    if (empty($query)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($conn->prepare($query)) ?: true;
}

