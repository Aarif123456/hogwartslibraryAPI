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
define('CHECKOUT_UPDATE_FAILED', 'Failed to update tables for checkout');

/* getting back the result of query as a JSON file */
function getExecutedResult($stmt)
{
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return $rows;
}

/* Catch exception that are set by MYSQLI_REPORT_ALL  */
function safeWriteQueries($stmt, $conn, $debug): bool
{
    try {
        return $stmt->execute() && $stmt->closeCursor();
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        if($conn->inTransaction()) {
            $conn->rollback();
        }
        if ($debug) {
            debugPrint($e, $conn);
        }
    }
    exit(WRITE_QUERY_FAILED);
}

function safeUpdateQueries($stmt, $conn, $debug): int
{
    try {
        if ($stmt->execute()) {
            $num = $stmt->rowCount();
            $stmt->closeCursor();
            if ($debug) {
                echo json_encode($conn->errorInfo()) . '<br />';
            }

            return $num;
        }
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        if($conn->inTransaction()) {
            $conn->rollback();
        }
        if ($debug) {
            debugPrint($e, $conn);
        }
    }
    exit(WRITE_QUERY_FAILED);
}

function safeInsertQueries($stmt, $conn, $debug): int
{
    try {
        if ($stmt->execute()) {
            $num = $conn->lastInsertId();
            $stmt->closeCursor();

            return $num;
        }
    } catch (Exception $e) {
        /* remove all queries from queue if error (undo) */
        if($conn->inTransaction()) {
            $conn->rollback();
        }
        if ($debug) {
            debugPrint($e, $conn);
        }
    }
    exit(WRITE_QUERY_FAILED);
}

function debugPrint($e, $conn)
{
    if (!empty($conn->errorCode())) {
        echo SQL_ERROR;
        echo json_encode($conn->errorInfo());
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