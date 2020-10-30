<?php //verify login
    /* Manually turn on error reporting */
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    
    require_once 'constants.php';
    /*Make sure user is validated */
    function validateUser($id){ //verify user did not fake authentication
        $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
        if ($cookie) {
            list ($userID, $token, $mac) = explode(':', $cookie); //get info from cookie
            if (!(hash_equals(hash_hmac('sha256',  $userID . ':' . $token, SECRET_KEY), $mac))) {
                return false;
            }
            $usertoken = $_SESSION['token']; //**  vulnerable if session was compromised 
            return hash_equals($usertoken, $token);
        }
      return false;
    }
    function checkSessionInfo(){
        return isset($_SESSION['userID']) && isset($_SESSION['token']) && isset($_SESSION['userType']);
    }
    /* logout function */
    function redirectToLogin(){
        destroy_session_and_data();
        //header("Location: /login");
        exit(); //in case the redirect didn't work
    }

    /* Utility functions to check user's type */
    function isProfessor(){
        return strcmp(trim($_SESSION['userType']??""),"professor")==0;
    }

    function isStudent(){
        return strcmp(trim($_SESSION['userType']??""),"student")==0;
    }

    function isHeadmaster(){
        return strcmp(trim($_SESSION['userType']??""),"headmaster")==0;
    }

    function isLibrarian(){
        return strcmp(trim($_SESSION['userType']??""),"librarian")==0;
    }

    /* utility function to make sure user has the correct permission*/
    function validateStudent($id){ 
        return isStudent() && validateUser($id);
    }

    function validateProfessor($id){ 
        return isProfessor() && validateUser($id);
    }
    function validateHeadmaster($id){
        return isHeadmaster() && validateUser($id); 
    }

    function validateLibrarian($id){
        return isLibrarian() && validateUser($id);
    }

    /* function to destroy session */
    function destroy_session_and_data(){
        $_SESSION = array();
        setcookie(session_name(), '', time() - 1, '/');
        $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
        if ($cookie) {
            list ($userID, $token, $mac) = explode(':', $cookie); //get info from cookie
            setcookie('rememberme',  $userID . ':' . $token . ':' . $mac , time() - 1, '/');
        }
        session_destroy();
    }

?>
