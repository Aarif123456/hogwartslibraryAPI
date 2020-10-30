<?php

/* Imports */
require_once 'charts/headmasterCharts.php';
require_once 'charts/librarianCharts.php';
require_once 'charts/professorCharts.php';
require_once 'charts/studentCharts.php';
require_once 'charts/guestCharts.php';
require_once 'error.php';

function getHeadmasterChartsResults($chartType, $conn){
    // get query
    $query = getHeadmasterCharts($chartType);
    // if no query return back null
    if(empty($query)) return null;
    // otherwise return back result from query
    return $conn->query($query);
}

function getLibrarianChartsResults($chartType, $conn){
    // get query
    $query = getLibrarianCharts($chartType);
    // if no query return back null
    if(empty($query)) return null;
    // otherwise return back result from query
    return $conn->query($query);
}

function getProfessorChartsResults($chartType, $conn){
    // get query
    $query = getProfessorCharts($chartType);
    // if no query return back null
    if(empty($query)) return null;
    // otherwise return back result from query
    return $conn->query($query);
}

function getStudentChartsResults($chartType, $conn){
    // get query
    $query = getStudentCharts($chartType);
    // if no query return back null
    if(empty($query)) return null;
    // otherwise return back result from query
    return $conn->query($query);
}

function getGuestChartsResults($chartType, $conn){
    // get query
    $query = getGuestCharts($chartType);
    // if no query return back null
    if(empty($query)) return null;
    // otherwise return back result from query
    return $conn->query($query);
}

?>