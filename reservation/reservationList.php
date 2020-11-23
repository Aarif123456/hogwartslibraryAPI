<?php
// TODO finish reservation list
/* Imports */
require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';
require_once __DIR__ . '/../repository/lists.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();
if (!(isValidPostVar('listType'))) {
    exit(MISSING_PARAMETERS);
}

if (checkSessionInfo() && validateUser($conn)) {
    $userID = isLibrarian() ? $_POST['userID'] ?? '' : $_SESSION['userID'];
    $debug = false;
    $listType = $_POST['listType'];
    $courseID = isset($_POST['courseID']) ?? '';
    $params = (object)[
        'userID' => (int)$userID,
        'courseID' => $courseID,
    ];
    $result = getReservationListResults($listType, $params, $conn);
    if (empty($result)) {
        exit(INVALID_LIST);
    }
    echo createQueryJSON($result);
} else {
    redirectToLogin();
}

$conn = null;


