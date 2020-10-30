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
    define('MISSING_PARAMETERS', 'Missing value');
    define('INVALID_PARAMETERS', 'Parameter do no have expected type');
    define('USERNAME_NOT_IN_TABLE', 'Username not taken');
    define('VALID_PASSWORD', 'Password is valid!');
    define('INVALID_PASSWORD', 'Invalid password.');
    define('INVALID_PASSCODE', 'Invalid passcode.');
    define('USERNAME_EXISTS', 'Username is taken');
    define('QUERY_FAILURE', 'Query failed please check the integrity of the database as well as the PHP commands used to run the query');
    define('NO_ROWS_RETURNED', 'No rows');
    define('UNAUTHORIZED_NO_LOGIN', 'not logged in!');
    define('UNAUTHORIZED_NOT_OWNER', 'the book is not checked out to you');
    define('INTERNAL_SERVER_ERROR', 'something went wrong:(');
    define('NO_BOOK_MATCHED', 'no book was selected');
    define('INVALID_LIST', 'Invalid list type');
    define('INVALID_CHART', 'Invalid chart type');
    define('INVALID_SEARCH_METHOD', 'Invalid search method');
    define('INVALID_COMMAND', 'No command with set parameter exist in endpoint');
    define('COURSE_ADDED', 'course added!');
    define('COURSE_DELETED', 'course deleted!');
    define('COMMAND_FAILED', 'Query failed to execute, ensure you use the correct values');
    define('ENROLLMENT_ADDED', 'Student enrolled in course');
    define('ENROLLMENT_DELETED', 'course enrollment deleted!');
    define('LIBRARIAN_DELETED', 'Librarian deactivated');
    define('STUDENT_ADDED', 'Student added');
    define('PROFESSOR_ADDED', 'Professor added');
    define('HEADMASTER_ADDED', 'Headmaster added');
    define('HEADMASTER_VERIFY_FAIL', 'Invalid password cannot reset user password');
    define('SELF_CHECKOUT', 'You can\'t sign out a book to your self!');
    
    /* error as functions*/
    /* We HTML entities any data coming back from the user before printing */
    function invalidUserType($userType){
        $userType = htmlentities($userType);
        return "'$userType' is not a recognized userType";
    }

    function passwordReset($uID){
        $printableUserID = htmlentities($uID);
        echo "Password has been reset for user with id $printableUserID";
    }
    function librarianCreated($librarianID){
        return "Created/Activated librarian with id: $librarianID";
    }

    function userCreated($userID){
        return "Created user with id: $userID";
    }
    function verifyUserType($userType){
        switch ($userType){
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

    function userTypeCreated($userType){
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

    function verifySelfCheckout($librarianID, $borrowedBy){
        if($librarianID == $borrowedBy){
          exit (SELF_CHECKOUT);
        }
    }

    function createQueryJSON($result, $noRowReturn = NO_ROWS_RETURNED){
        if(!$result) exit( QUERY_FAILURE);
        $arr = $result->fetch_all(MYSQLI_ASSOC);
        if(!$arr) exit($noRowReturn);
        $result->close();
        return json_encode($arr); 
    }

    /* utility function for post, get and session if enough function this will go to it's own file*/
    function isValidPostVar($varName){
        return isset($_POST[$varName]) && !(empty($_POST[$varName]));
    }

    function isValidResquestVar($varName){
        return isset($_REQUEST[$varName]) && !(empty($_REQUEST[$varName]));
    }


?>