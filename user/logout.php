<?php
/* program to verify login*/

require_once '../config/apiReturn.php';
require_once '../config/authenticate.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();
destroy_session_and_data();
echo USER_LOGGED_OUT;