<?php
/* Required header */
header('Access-Control-Allow-Origin: https://abdullaharif.tech'); 
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');    // cache for 1 day
session_cache_limiter('private_no_expire');
if (!session_id()) {
    session_start();
}

/* Imports */
require_once 'config/apiReturn.php';
require_once 'config/constants.php';
require_once 'config/authenticate.php';
require_once 'repository/database.php';
require_once 'repository/manageReservationRepo.php';

/* Connect to database */
$conn = getConnection();

if(!(isValidPostVar('courseID') && isValidPostVar('bookISBN'))) exit(MISSING_PARAMETERS);

if (checkSessionInfo() && validateHeadmaster($_SESSION['userID']))   
{  
    $bookISBN = $_POST['bookISBN'];
    $courseID = $_POST['courseID'];
    $professorID = /* TODO From session if prof otherwise get from post if librarian */;
    if(insertReservation($courseID, $bookISBN, $conn))  echo RESERVATION_ADDED;
    else echo COMMAND_FAILED;
} else{
    redirectToLogin();
}

$conn->close();  

?>
