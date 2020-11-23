<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/charts.php';

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


