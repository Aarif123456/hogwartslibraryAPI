<?php

/* Imports */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/authenticate.php';

function getProfessorID(): int
{
    if (isLibrarian()) {
        return $_POST['userID'] ?? '';
    }

    return isProfessor() ? $_SESSION['userID'] : '';
}