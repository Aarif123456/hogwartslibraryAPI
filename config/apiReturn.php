<?php
/* Stores the return of the API, created to make localization easier
Some API return that come from SQL related error are located in repository/error.php
*/

/* Manually turn on error reporting */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('session.cookie_secure', 1);
error_reporting(E_ALL);
/* Define the strings the api will return  */

/* constants */
define('ALREADY_LOGGED_IN', 'ERROR: User is already logged in');
define('BOOK_FOUND', 'The book is now found!!!');
define('BOOK_INELIGIBLE_FOR_CHECKOUT', 'Book is ineligible to be checked out to the user');
define('BOOK_LOST_RETURN', 'Book is has successfully been reported as lost');
define('BOOK_MAXIMUM_RENEWED', 'Book has been renewed the maximum amount of times');
define('BOOK_PLACED_ON_HOLD', 'Book was placed on hold.');
define('BOOK_RENEW_FAILURE_DEBUG', 'Book also cannot be renewed if the book is not checked out to user');
define('BOOK_RENEWED', 'Book renewed!');
define('COMMAND_FAILED', 'Query failed to execute, ensure you use the correct values');
define('COURSE_ADDED', 'course added!');
define('COURSE_DELETED', 'course deleted!');
define('ENROLLMENT_ADDED', 'Student enrolled in course');
define('ENROLLMENT_DELETED', 'course enrollment deleted!');
define('HEADMASTER_ADDED', 'Headmaster added');
define('HEADMASTER_VERIFY_FAIL', 'Invalid password cannot reset user password');
define('HOLD_CANCELLED_RETURN', 'Hold cancelled.');
define('HOLD_EXISTS', 'User already has an active hold on the book');
define('HOLD_NOT_EXISTS', 'Hold does not exist');
define(
    'HOLD_READY_FOR_PICKUP',
    'A copy of the book is reserved for you at the library. Please pick it within a month.'
);
define('INTERNAL_SERVER_ERROR', 'something went wrong:(');
define('INVALID_CHART', 'Invalid chart type');
define('INVALID_LIST', 'Invalid list type');
define('INVALID_PARAMETERS', 'Parameter do no have expected type');
define('INVALID_PASSCODE', 'Invalid passcode.');
define('INVALID_PASSWORD', '{"success":false}');
define('INVALID_SEARCH_METHOD', 'Invalid search method');
define('LIBRARIAN_DELETED', 'Librarian deactivated');
define('MISSING_PARAMETERS', 'Missing value');
define('NO_ROWS_RETURNED', 'No rows');
define('PAID_SUCCESSFULLY', 'The fine is paid');
define('PROFESSOR_ADDED', 'Professor added');
define(
    'QUERY_FAILURE',
    'Query failed please check the integrity of the database as well as the PHP commands used to run the query'
);
define('RETURN_FAILED', 'Failed to update tables for return, make sure book is not already returned available');
define('RESERVATION_DELETED', 'book reservation deleted');
define('RESERVATION_RESET_FAILED', 'Unable to reset old reservation. Failed to reserve books');
define('SELF_CHECKOUT', 'You can\'t sign out a book to your self!');
define('STUDENT_ADDED', 'Student added');
define('UNAUTHORIZED_NO_LOGIN', 'not logged in!');
define('UNAUTHORIZED_NOT_OWNER', 'the book is not checked out to you');
define('USER_BLACKLISTED', 'user is blacklisted');
define('USER_INELIGIBLE_FOR_CHECKOUT', 'User is either blacklisted or above their limit to checkout the book');
define('USER_LOGGED_OUT', 'User has successfully logged out');
define('USERNAME_EXISTS', 'Username is taken');
define('USERNAME_NOT_IN_TABLE', 'Username not taken');

/* error as functions*/
/* We HTML entities any data coming back from the user before printing */
function invalidUserType($userType): string
{
    $userType = htmlentities($userType);

    return "'$userType' is not a recognized userType";
}

function authenticatedSuccesfully($userType): string
{
    $userType = htmlentities($userType);
    $return = (object)[
        'success' => true,
        'userType' => $userType
    ];

    return json_encode($return);
}

function passwordReset($uID): string
{
    $printableUserID = htmlentities($uID);

    return "Password has been reset for user with id $printableUserID";
}

function librarianCreated($librarianID): string
{
    return "Created/Activated librarian with id: $librarianID";
}

function userCreated($userID): string
{
    return "Created user with id: $userID";
}

function successfulCheckout($bookBarcode, $borrowedBy, $librarianID): string
{
    $bookBarcode = htmlentities($bookBarcode);
    $borrowedBy = htmlentities($borrowedBy);
    $librarianID = htmlentities($librarianID);

    return "Transaction successful : Book with barcode $bookBarcode has been issued to $borrowedBy by the librarianID $librarianID";
}

function successfulReturn($todayDate, $bookBarcode): string
{
    $bookBarcode = htmlentities($bookBarcode);

    return "book with barcode $bookBarcode has been returned on $todayDate!";
}

function addedReservationReturn($bookISBN, $numCopies): string
{
    $numCopies = htmlentities($numCopies);

    return "Successfully reserved $numCopies copies of book for class";
}

function verifyUserType($userType): bool
{
    switch ($userType) {
        case 'user':       // INTENTIONAL FALLTHROUGH
        case 'student':    // INTENTIONAL FALLTHROUGH
        case 'professor':  // INTENTIONAL FALLTHROUGH
        case 'headmaster': // INTENTIONAL FALLTHROUGH
        case 'librarian':  // INTENTIONAL FALLTHROUGH
            return true;
        default:
            return false;
    }
}

function isValidHouse($house): bool
{
    switch ($house) {
        case 'Gryffindor':       // INTENTIONAL FALLTHROUGH
        case 'Ravenclaw':    // INTENTIONAL FALLTHROUGH
        case 'Hufflepuff':  // INTENTIONAL FALLTHROUGH
        case 'Slytherin': // INTENTIONAL FALLTHROUGH
            return true;
        default:
            return false;
    }
}

function userTypeCreated($userType): string
{
    switch ($userType) {
        case 'student':
            return STUDENT_ADDED;
        case 'professor':
            return PROFESSOR_ADDED;
        case 'headmaster':
            return HEADMASTER_ADDED;
        default:
            return '';
    }
}

function verifySelfCheckout($librarianID, $borrowedBy)
{
    if ($librarianID == $borrowedBy) {
        exit (SELF_CHECKOUT);
    }
}

function createQueryJSON($arr, $noRowReturn = NO_ROWS_RETURNED)
{
    if (!$arr) {
        exit($noRowReturn);
    }

    return json_encode($arr);
}

/* Required header */
function getHeader()
{
    // header('Access-Control-Allow-Origin: https://abdullaharif.tech');
    // header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Origin: https://localhost:3000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: X-Requested-With,content-type');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day

}

function startSession()
{
    $status = session_status();
    if (PHP_SESSION_DISABLED === $status) {
        // That's why you cannot rely on sessions!
        return;
    }

    if (PHP_SESSION_NONE === $status) {
        session_cache_limiter('private_no_expire');
        session_start();
    }
}

function requiredHeaderAndSessionStart()
{
    getHeader();
    startSession();
}

/* utility function for post, get and session if enough function this will go to it's own file*/
function isValidPostVar($varName): bool
{
    return isset($_POST[$varName]) && $_POST[$varName];
}

function isValidRequestVar($varName)
{
    return isset($_REQUEST[$varName]) && $_REQUEST[$varName];
}

