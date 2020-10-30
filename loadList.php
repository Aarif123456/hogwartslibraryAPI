<?php
/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech'); 
header('Access-Control-Allow-Credentials: true');
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
require_once 'repository/lists.php';

/* Connect to database */
$conn = getConnection();

$result = null;
if (checkSessionInfo() && validateUser($_SESSION['userID']))   
{
    $userType = trim($_SESSION['userType']);
    if(isValidPostVar('listType')){
        $listType = $_POST['listType'] ?? "";
        if(isLibrarian()) {
            $result = getLibrarianListResults($listType, $conn);
        }
        else if (isHeadmaster()){
            $result = getHeadmasterListResults($listType, $conn);  
        }
        if(empty($result)) exit(INVALID_LIST);
        echo createQueryJSON($result);
    } else {
        exit(MISSING_PARAMETERS);
    }
} else{
    redirectToLogin();
}

$conn->close();


?>

