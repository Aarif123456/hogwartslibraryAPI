<?php

/* Error handling for calls to the database */
define('WRITE_QUERY_FAILED', 'Failed to update the database');
define('SQL_ERROR', 'The following SQL error was detected:');
define('PHP_EXCEPTION', 'The following exception was thrown:');
define(
    'MISSING_PARAMETER_FOR_USER_TYPE',
    'Missing required parameter for selected user type'
);
define(
    'CHECKOUT_FAILED',
    'Failed to checkout book to user. ' .
    'The book may be on hold to another user ' .
    'or the reserved to a class that the user is not taking'
);
define('CHECKOUT_UPDATE_FAILED', "Failed to update tables for checkout ");

/* getting back the result of query as a JSON file */
function getExecutedResult($stmt)
{
    //find all transaction
    $stmt->execute();

    return $result = $stmt->get_result();
}

/* Catch exception that are set by MYSQLI_REPORT_ALL  */
function safeWriteQueries($stmt, $conn, $debug)
{
    try {
        return $stmt->execute() && $stmt->close();
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        $conn->rollback();
        if ($debug) {
            debugPrint($e, $conn);
        }
        exit(WRITE_QUERY_FAILED);
    }
}

function debugPrint($e, $conn)
{
    if (!empty($conn->error)) {
        echo SQL_ERROR;
        echo $conn->error;
    }
    echo PHP_EXCEPTION;
    echo $e;
}

function debugQuery($affectedRow, $success, $functionName): string
{
    return nl2br(
        "FUNCTION $functionName: row affected = $affectedRow \n FUNCTION $functionName: successful = $success"
    );
}