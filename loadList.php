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

$result = null;
if (checkSessionInfo() && validateUser()) {
    $userType = trim($_SESSION['userType']);
    if (isValidPostVar('listType')) {
        $listType = $_POST['listType'] ?? "";
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
        exit(MISSING_PARAMETERS);
    }
} else {
    redirectToLogin();
}

$conn->close();




