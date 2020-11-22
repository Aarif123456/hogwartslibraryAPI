<?php
/* program to verify login*/

require_once '../config/apiReturn.php';
require_once '../config/authenticate.php';
require_once '../repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if(logout($conn)){
    echo USER_LOGGED_OUT;
} else{
    exit(INTERNAL_SERVER_ERROR);
}
