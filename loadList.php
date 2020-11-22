<?php

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/lists.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('listType'))) {
    exit(MISSING_PARAMETERS);
}

$result = null;
if (checkSessionInfo() && validateUser($conn)) {
    $userType = trim($_SESSION['userType']);
    $listType = $_POST['listType'] ?? '';
    if (isLibrarian()) {
        $result = getLibrarianListResults($listType, $conn);
    } else {
        if (isHeadmaster()) {
            $result = getHeadmasterListResults($listType, $conn);
        }
    }
    if (empty($result)) {
        exit(INVALID_LIST);
    }
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;




