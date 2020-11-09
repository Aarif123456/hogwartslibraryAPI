<?php
/*Runs once a day to mark holds that are expired as expired */

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';
require_once 'database.php';

/* Connect to database */
$conn = getConnection();

$stmt = $conn->prepare("UPDATE holds SET status=? WHERE (status=? OR status=?) AND CURDATE() > holdExpiryDate ");
$holdExpired = HOLD_EXPIRED;
$readyForPickup = HOLD_ACTIVE;
$holdPending = HOLD_IN_QUEUE;
$stmt->bind_param("iii", $holdExpired, $readyForPickup, $holdPending);
echo safeWriteQueries($stmt, $conn, false);
