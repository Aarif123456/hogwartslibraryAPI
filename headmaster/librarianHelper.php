<?php

function librarianEndpointSetup()
{
    /* Imports */
    require_once __DIR__ . '/../config/apiReturn.php';
    require_once __DIR__ . '/../config/constants.php';
    require_once __DIR__ . '/../config/authenticate.php';
    require_once __DIR__ . '/../repository/database.php';
    require_once __DIR__ . '/../repository/manageLibrarianRepo.php';

    /* Set required header and session start */
    requiredHeaderAndSessionStart();


    if (!(isValidPostVar('userID'))) {
        exit(MISSING_PARAMETERS);
    }
    if (!(is_numeric($_POST['userID']))) {
        exit(INVALID_PARAMETERS);
    }
}