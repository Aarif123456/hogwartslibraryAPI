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
$website = "https://abdullaharif.tech/hogwartslibrary";

if (checkSessionInfo() && validateUser()) {
    $username = htmlentities($_SESSION['username']);
    $screenwidth = $_POST['screenwidth'];

    if ($screenwidth < 1024) {
        echo "<ul> <li><a href=\"$website/docs/catalogue/search\">" .
             "<img src=\"$website/resources/images/search.png\" class=\"img-responsive\" alt=\"search\">" .
             "</a></li> <li><a href=\"$website/docs/catalogue/userDashboard\">";
    }
    if ($screenwidth < 505) { //cell phone just use initial
        $expr = '/(?<=\s|^)[a-z]/i'; //Get initials
        preg_match_all($expr, $username, $matches);
        $result = implode('.', $matches[0]);
        $result = strtoupper($result);
        echo "$result </a></li> ";
    } else {
        if ($screenwidth < 1024) { //tablet only use full name
            echo "$username </a></li> ";
        } else { //for computer display full text
            echo "<ul> <li><a href=\"$website/docs/catalogue/search\">Search catalogue </a></li>" .
                 "<li><a href=\"$website/docs/catalogue/userDashboard\">";
            echo "Welcome $username </a></li> ";
        }
    }

    echo "<div class=\"clearfix\"> </div> </ul>";
} else {
    exit(UNAUTHORIZED_NO_LOGIN);
}

