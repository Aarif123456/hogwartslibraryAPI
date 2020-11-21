<?php

/* Imports */
require_once '../config/apiReturn.php';
require_once '../config/constants.php';
require_once '../config/authenticate.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

if (checkSessionInfo() && validateUser()) {
    echo "true";
} else {
    echo "false";
}

