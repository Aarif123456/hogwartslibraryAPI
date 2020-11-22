<?php
/*Runs once a day to mark holds that are expired as expired */

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function updateHoldTable($conn, $debug = false)
{
    $stmt = $conn->prepare(
        '
                            UPDATE 
                                holds 
                            SET 
                                status = :holdExpired
                            WHERE 
                                (
                                    status = :readyForPickup 
                                    OR status = :holdPending
                                ) 
                                AND CURDATE() > holdExpiryDate 
                        '
    );
    $stmt->bindValue(':holdExpired', HOLD_EXPIRED, PDO::PARAM_INT);
    $stmt->bindValue(':readyForPickup', HOLD_ACTIVE, PDO::PARAM_INT);
    $stmt->bindValue(':holdPending', HOLD_IN_QUEUE, PDO::PARAM_INT);
    echo safeWriteQueries($stmt, $conn, $debug);
}
