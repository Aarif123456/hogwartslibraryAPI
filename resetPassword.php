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
/* Used to double check that the headmaster is the one resetting the password */
require_once 'repository/headmasterVerifyRepo.php';
require_once 'repository/resetPassword.php';

/* Connect to database */
$conn = getConnection();

if(!(isValidPostVar('uID') && isValidPostVar('uPassword') 
    && isValidPostVar('developerPassword')))
    exit(MISSING_PARAMETERS);

if (checkSessionInfo() && validateHeadmaster($_SESSION['userID']))   
{
    $developerPassword = $_POST['developerPassword'];
    $developerID = $_SESSION['userID'];
    $result = queryDeveloperPassword($developerID, $conn);
    $row = $result->fetch_assoc();
    $password = $_POST['uPassword'];
    $uID = $_POST['uID'];
    /* Make sure the password is correct */
    if ($result->num_rows != 0 && password_verify($developerPassword, $row['password'])) {
        /* Reset invalid password count, because admin entered the correct password */
        $_SESSION['invalidpasswordCount'] = 0;
        if(resetQuery($uID, $password, $conn)) echo passwordReset($uID);
    } else{
        wrongDeveloperPassword();
    }
} else{
    redirectToLogin();
}


$conn->close();  
function wrongDeveloperPassword(){
    $_SESSION['invalidpasswordCount'] = ($_SESSION['invalidpasswordCount'] ?? 0)+1;
    if($_SESSION['invalidpasswordCount']>3){
        redirectToLogin();
    }
    exit(HEADMASTER_VERIFY_FAIL);
}
?>
