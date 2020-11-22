<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';
require_once '../repository/charts.php';

/* Set required header and session start */
header('Content-Type: application/json');
requiredHeaderAndSessionStart();


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

$conn = null;


