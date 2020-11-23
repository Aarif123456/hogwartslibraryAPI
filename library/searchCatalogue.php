<?php

/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/searchCatalogueRepo.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (isValidRequestVar('searchType') && isset($_REQUEST['searchWord'])) {
    $searchType = $_REQUEST['searchType'];
    if (!validSearchMethod($searchType)) {
        exit(INVALID_SEARCH_METHOD);
    }
    $term = $_REQUEST['searchWord'];
    $result = queryCatalog($searchType, $term, $conn);
    echo createQueryJSON($result);
} else {
    echo MISSING_PARAMETERS;
}
$conn = null;



