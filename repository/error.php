<?php 
    /* Error handling for calls to the database */
    define('WRITE_QUERY_FAILED', 'Failed to update the database');
    define('SQL_ERROR', 'The following sql error was detected:');
    define('PHP_EXCEPTION', 'The following exception was thrown:');
    define('MISSING_PARAMETER_FOR_USER_TYPE', 
            'Missing required parameter for selected user type' );

    /* getting back the result of query as a JSON file */
    function getExecutedResult($stmt){
        //find all transaction 
        $stmt->execute();
        return $result = $stmt->get_result();
    }

    /* Catch exception that are set by MYSQLI_REPORT_ALL  */
    function safeWriteQueries($stmt, $conn, $debug){
        try{ //prepare and insert to avoid injection
            $conn->autocommit(FALSE); //turn off transactions
            $stmt->execute();
            $stmt->close();
            $conn->autocommit(TRUE);
            return true;
        } catch(Exception $e) {
            /* remove all queries from queue if error (undo) */
            $conn->rollback(); 
            if($debug) debugPrint($e, $conn);
            exit(WRITE_QUERY_FAILED);
        }
        return false;
    }

    function debugPrint($e, $conn){
        if(!empty($conn->error)) {
            echo SQL_ERROR;
            echo $conn->error;
        }
        echo PHP_EXCEPTION;
        echo $e;
    }
?>