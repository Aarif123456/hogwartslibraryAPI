<?php

/* Imports */
require_once __DIR__ . '/config/apiReturn.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/authenticate.php';
require_once __DIR__ . '/repository/database.php';
require_once __DIR__ . '/repository/lists.php';

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
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;




