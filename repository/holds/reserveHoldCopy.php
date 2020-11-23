<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';
require_once __DIR__ . '/reserveCopyForCourse.php';
require_once __DIR__ . '/reserveCopyNoReservation.php';

/* Go through hold table and update book */
function reserveCopy($bookISBN, $holderID, $courseID, $conn, $debug = false)
{
    $stmt = $courseID ? getReserveCopyForCourse($bookISBN, $holderID, $courseID, $conn)
        : getReserveCopy($bookISBN, $holderID, $conn);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);

    return !empty($numRows);
}
