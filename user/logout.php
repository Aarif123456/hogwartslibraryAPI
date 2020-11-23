<?php
/* program to verify login*/

require_once __DIR__ . '/../config/apiReturn.php';
require_once __DIR__ . '/../config/authenticate.php';
require_once __DIR__ . '/../repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (logout($conn)) {
    echo USER_LOGGED_OUT;
} else {
    exit(INTERNAL_SERVER_ERROR);
}
