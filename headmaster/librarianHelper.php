<?php

function librarianEndpointSetup()
{
    /* Imports */
    require_once '../config/apiReturn.php';
    require_once '../config/constants.php';
    require_once '../config/authenticate.php';
    require_once '../repository/database.php';
    require_once '../repository/manageLibrarianRepo.php';

    /* Set required header and session start */
    requiredHeaderAndSessionStart();


    if (!(isValidPostVar('userID'))) {
        exit(MISSING_PARAMETERS);
    }
    if (!(is_numeric($_POST['userID']))) {
        exit(INVALID_PARAMETERS);
    }
}