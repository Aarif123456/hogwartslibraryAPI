<?php
/* Stores the return of the API, created to make localization easier
Some API return that come from SQL related error are located in repository/error.php
*/

/* Manually turn on error reporting */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/* Define the strings the api will return  */

/* constants */
define('BOOK_FOUND', 'The book is now found!!!');
define('BOOK_INELIGIBLE_FOR_CHECKOUT', 'Book is ineligible to be checked out to the user');
define('BOOK_PLACED_ON_HOLD', 'Book was placed on hold.');
define('COMMAND_FAILED', 'Query failed to execute, ensure you use the correct values');
define('COURSE_ADDED', 'course added!');
define('COURSE_DELETED', 'course deleted!');
define('ENROLLMENT_ADDED', 'Student enrolled in course');
define('ENROLLMENT_DELETED', 'course enrollment deleted!');
define('HEADMASTER_ADDED', 'Headmaster added');
define('HEADMASTER_VERIFY_FAIL', 'Invalid password cannot reset user password');
define('HOLD_EXISTS', 'User already has an active hold on the book');
define(
    'HOLD_READY_FOR_PICKUP',
    'A copy of the book is reserved for you at the library. Please pick it within a month.'
);
define('INTERNAL_SERVER_ERROR', 'something went wrong:(');
define('INVALID_CHART', 'Invalid chart type');
define('INVALID_COMMAND', 'No command with set parameter exist in endpoint');
define('INVALID_LIST', 'Invalid list type');
define('INVALID_PARAMETERS', 'Parameter do no have expected type');
define('INVALID_PASSCODE', 'Invalid passcode.');
define('INVALID_PASSWORD', 'Invalid password.');
define('INVALID_SEARCH_METHOD', 'Invalid search method');
define('LIBRARIAN_DELETED', 'Librarian deactivated');
define('MISSING_PARAMETERS', 'Missing value');
define('NO_BOOK_MATCHED', 'no book was selected');
define('NO_ROWS_RETURNED', 'No rows');
define('PAID_SUCCESSFULLY', 'The fine is paid');
define('PROFESSOR_ADDED', 'Professor added');
define(
    'QUERY_FAILURE',
    'Query failed please check the integrity of the database as well as the PHP commands used to run the query'
);
define('RETURN_FAILED', 'Failed to update tables for return, make sure book is not already returned available');
define('SELF_CHECKOUT', 'You can\'t sign out a book to your self!');
define('STUDENT_ADDED', 'Student added');
define('UNAUTHORIZED_NO_LOGIN', 'not logged in!');
define('UNAUTHORIZED_NOT_OWNER', 'the book is not checked out to you');
define('USER_BLACKLISTED', "user is blacklisted");
define('USER_INELIGIBLE_FOR_CHECKOUT', 'User is either blacklisted or above their limit to checkout the book');
define('USER_LIMIT_REACHED', "User has reached their limit. They need to return some books!");
define('USERNAME_EXISTS', 'Username is taken');
define('USERNAME_NOT_IN_TABLE', 'Username not taken');
define('VALID_PASSWORD', 'Password is valid!');


/* error as functions*/
/* We HTML entities any data coming back from the user before printing */
function invalidUserType($userType): string
{
    $userType = htmlentities($userType);

    return "'$userType' is not a recognized userType";
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
        case "user":       // INTENTIONAL FALLTHROUGH
        case "student":    // INTENTIONAL FALLTHROUGH
        case "professor":  // INTENTIONAL FALLTHROUGH
        case "headmaster": // INTENTIONAL FALLTHROUGH
        case "librarian":  // INTENTIONAL FALLTHROUGH
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
            return "";
    }
}

function verifySelfCheckout($librarianID, $borrowedBy)
{
    if ($librarianID == $borrowedBy) {
        exit (SELF_CHECKOUT);
    }
}

function createQueryJSON($result, $noRowReturn = NO_ROWS_RETURNED)
{
    if (!$result) {
        exit(QUERY_FAILURE);
    }
    $arr = $result->fetch_all(MYSQLI_ASSOC);
    if (!$arr) {
        exit($noRowReturn);
    }
    $result->close();

    return json_encode($arr);
}

/* Required header */
function getHeader()
{
    header('Access-Control-Allow-Origin: https://abdullaharif.tech');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day

}

function startSession()
{
    session_cache_limiter('private_no_expire');
    if (!session_id()) {
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

