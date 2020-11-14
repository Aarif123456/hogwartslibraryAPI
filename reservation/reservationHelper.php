<?php

/* Imports */
require_once '../config/constants.php';
require_once '../config/authenticate.php';

function getProfessorID(): int
{
    if (isLibrarian()) {
        return $_POST['userID'] ?? "";
    }

    return isProfessor() ? $_SESSION['userID'] : "";
}