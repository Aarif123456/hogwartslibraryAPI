<?php
/* THIS WILL BE ARCHIVED */

/* Imports */
require_once __DIR__ . '/config/apiReturn.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/authenticate.php';
require_once __DIR__ . '/repository/database.php';

/* Set required header and session start */
requiredHeaderAndSessionStart();

/* Connect to database */
$conn = getConnection();

if (checkSessionInfo() && validateUser($conn)) {
    $userType = trim($_SESSION['userType']);
    if (isLibrarian()) {
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
    } elseif (isHeadmaster()) {
        $pageTitle['0'] = 'Browse collection';
        $pageTitle['1'] = 'Manage Librarians';
        $pageTitle['2'] = 'Reset Passwords';
        $pageTitle['3'] = 'Manage Courses';
        $pageTitle['4'] = 'Manage Enrollment';
        $pageTitle['5'] = 'Manage Request';

        $pageLinks['0'] = 'catalogue/search';
        $pageLinks['1'] = 'headmaster/manageLibrarian';
        $pageLinks['2'] = 'headmaster/resetPassword';
        $pageLinks['3'] = 'headmaster/manageCourses';
        $pageLinks['4'] = 'headmaster/manageEnrollment';
        $pageLinks['5'] = 'headmaster/manageRequest';
    } else {
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

        if (isProfessor()) {
            $pageTitle['5'] = 'Reserved book';
            $pageLinks['5'] = 'catalogue/professorReservationMenu';
        } else {
            $pageTitle['5'] = 'Reserved book';
            $pageLinks['5'] = 'catalogue/studentReservationMenu';
        }
    }
    //$arr['userType']  = $userType;                 
    $arr['pageTitle'] = $pageTitle;
    $arr['pageLinks'] = $pageLinks;
    echo json_encode($arr);
} else {
    redirectToLogin();
}

