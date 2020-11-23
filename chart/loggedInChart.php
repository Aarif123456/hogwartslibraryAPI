<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/charts.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $userType = trim($_SESSION['userType']);
    /* Some users have a default chart that loads on the dashboard */
    $chartType = $_POST['chartType'] ?? $defaultChart[$userType] ?? '';
    $result = null;
    switch ($userType) {
        case 'headmaster': // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getHeadmasterChartsResults($chartType, $conn);
            if (!empty($result)) {
                break;
            }
        case 'librarian':  // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getLibrarianChartsResults($chartType, $conn);
            if (!empty($result)) {
                break;
            }
        case 'professor':  // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getProfessorChartsResults($chartType, $conn);
            if (!empty($result)) {
                break;
            }
        case 'student':
            $result = getStudentChartsResults($chartType, $conn);
            break;
        default:
            exit(invalidUserType($userType));
    }
    if (empty($result)) {
        exit(INVALID_CHART);
    }
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;




