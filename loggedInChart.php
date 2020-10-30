<?php
/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech'); 
header('Access-Control-Allow-Credentials: true');
// header('Content-Type: application/json');
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

if (checkSessionInfo() && validateUser($_SESSION['userID']))   
{  
    $userType = trim($_SESSION['userType']);
    /* Some users have a default chart that loads on the dashboard */
    $chartType = $_POST['chartType'] ?? $defaultChart[$userType] ?? "";
    $result = null;
    switch ($userType) {
        case "headmaster": // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getHeadmasterChartsResults($chartType, $conn);
            if(!empty($result)) break;
        case "librarian":  // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getLibrarianChartsResults($chartType, $conn);
            if(!empty($result)) break;
        case "professor":  // INTENTIONAL FALL THROUGH FOR ESCALATING PERMISSION
            $result = getProfessorChartsResults($chartType, $conn);
            if(!empty($result)) break;
        case "student":    
            $result = getStudentChartsResults($chartType, $conn);
            break;
        default:
            exit(invalidUserType($userType));
    }
    if(empty($result)) exit(INVALID_CHART);
    echo createQueryJSON($result);
} else{
    redirectToLogin();
}

$conn->close();
?>



