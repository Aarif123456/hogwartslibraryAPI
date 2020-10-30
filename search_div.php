<?php
/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech');
header('Access-Control-Allow-Credentials: true');
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/searchCatalogueRepo.php';

/* Connect to database */
$conn = getConnection();

if (isValidResquestVar('searchType') && isset($_REQUEST['searchWord']))
{
    $searchType = $_REQUEST['searchType'];
    $term = $_REQUEST['searchWord'];
    $result = queryCatalog($searchType, $term, $conn);
    echo createQueryJSON($result);
} else{
    echo MISSING_PARAMETERS;
}
$conn->close();

?>

