<?php
/* THIS WILL BE ARCHIVED */
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

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($_SESSION['userID']))   
{  
    $userType = trim($_SESSION['userType']);
    if(validateLibrarian($_SESSION['userID'])){
        $pageTitle['0'] = 'Check out';
        $pageTitle['1'] = 'Returns';
        $pageTitle['2'] = 'Holds';
        $pageTitle['3'] = 'Fines';
        $pageTitle['4'] = 'Request List';
        $pageTitle['5'] = 'Reserve books';

        $pageLinks['0'] = 'librarian/checkOut';
        $pageLinks['1'] = 'librarian/return';
        $pageLinks['2'] = 'librarian/holds';
        $pageLinks['3'] = 'librarian/fines';
        $pageLinks['4'] = 'librarian/request';
        $pageLinks['5'] = 'librarian/reserve';
    }

    elseif(validateHeadmaster($_SESSION['userID'])) {
        $pageTitle['0'] = 'Browse collection';
        $pageTitle['1'] = 'Manage Librarians';
        $pageTitle['2'] = "Reset Passwords";
        $pageTitle['3'] = 'Manage Courses';
        $pageTitle['4'] = 'Manage Enrollment';
        $pageTitle['5'] = 'Manage Request';

        $pageLinks['0'] = 'catalogue/search';
        $pageLinks['1'] = 'headmaster/manageLibrarian';
        $pageLinks['2'] = 'headmaster/resetPassword';
        $pageLinks['3'] = 'headmaster/manageCourses';
        $pageLinks['4'] = 'headmaster/manageEnrollment';
        $pageLinks['5'] = 'headmaster/manageRequest';
            
    }

    elseif(validateUser($_SESSION['userID'])){
        $pageTitle['0'] = 'Browse collection';
        $pageTitle['1'] = 'Checked out';
        $pageTitle['2'] = 'Holds';
        $pageTitle['3'] = 'Fines';
        $pageTitle['4'] = 'Submit Request';
        

        $pageLinks['0'] = 'catalogue/search';
        $pageLinks['1'] = 'catalogue/checkedOutMenu';
        $pageLinks['2'] = 'catalogue/holdMenu';
        $pageLinks['3'] = 'catalogue/fineMenu';
        $pageLinks['4'] = 'catalogue/submitRequest';

        if(strcmp($userType, "professor")==0){
            $pageTitle['5'] = 'Reserved book';
            $pageLinks['5'] = 'catalogue/professorReservationMenu';
        }
        else{
            $pageTitle['5'] = 'Reserved book';
            $pageLinks['5'] = 'catalogue/studentReservationMenu';
        }        
    }
    else{
        echo UNAUTHORIZED_NO_LOGIN;
        exit();
    }
    //$arr['userType']  = $userType;                 
    $arr['pageTitle'] =$pageTitle;
    $arr['pageLinks'] =$pageLinks;
    echo json_encode($arr);
        
}
else{
    echo UNAUTHORIZED_NO_LOGIN;
    exit();
}

$conn->close();
?>

