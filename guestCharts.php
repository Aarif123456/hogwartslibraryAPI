<?php

/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/charts.php';

/* Connect to database */
$conn = getConnection();

//guest charts
if (isValidRequestVar('chartType')) {
    $chartType = trim($_REQUEST['chartType']);
    $result = getGuestChartsResults($chartType, $conn);
    if (empty($result)) {
        exit(INVALID_CHART);
    }
    echo createQueryJSON($result);
}

$conn->close();


