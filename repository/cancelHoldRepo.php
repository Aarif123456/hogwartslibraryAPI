<?php

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

/* Cancel the hold - change hold status, decrease hold count by one */
function cancelHold($holdID, $holderID, $conn, $debug = false)
{
    $query =
        "
            UPDATE holds 
                LEFT OUTER JOIN bookItem ON holds.reservedCopy = bookItem.bookBarcode
                INNER JOIN books ON holds.bookISBN = books.bookISBN
            SET
                holds.status = ?,
                books.holds = books.holds - 1,
                bookItem.status = ?
            WHERE
                holds.holdID = ?
                AND holds.holderID = ?
                AND (
                        holds.status = ?
                        OR holds.status = ?
                    ) 

        ";
    $holdCancelled = HOLD_CANCELLED;
    $holdActive = HOLD_ACTIVE;
    $holdInQueue = HOLD_IN_QUEUE;
    $bookAvailable = BOOK_AVAILABLE;
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iiiiii",
        $holdCancelled,
        $bookAvailable,
        $holdID,
        $holderID,
        $holdActive,
        $holdInQueue
    );
    $numRows = safeUpdateQueries($stmt, $conn, $debug);

    return $numRows === 3;
}

/* Reserve newly freed copy to user next in line for holds */
function reserveHoldCopy($holdID, $holderID, $conn, $debug = false)
{
    $query =
        "
           UPDATE
               (
               SELECT
                   *
               FROM
                   holds
               WHERE
                   holdID = ?
           ) AS cancelledHold
           INNER JOIN (
               SELECT *
               FROM
                   holds AS h
               LEFT OUTER JOIN courseEnrolled ON courseEnrolled.studentID = h.holderID
               WHERE
                    h.status = ?
                    AND h.bookISBN IN (SELECT bookISBN FROM holds WHERE holdID = ? )
               ORDER BY
                   TIMESTAMP
               LIMIT 1
           ) AS filteredHold
           INNER JOIN bookItem ON bookItem.bookBarcode = cancelledHold.reservedCopy
           INNER JOIN holds ON filteredHold.holdID = holds.holdID
           SET
               holds.status = ?,
               holds.reservedCopy = cancelledHold.reservedCopy,
               holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
               bookItem.status = ?
           WHERE
               bookItem.status = ?
        ";
    $stmt = $conn->prepare($query);
    $holdInQueue = HOLD_IN_QUEUE;
    $activeHold = HOLD_ACTIVE;
    $onHold = BOOK_ON_HOLD;
    $bookAvailable = BOOK_AVAILABLE;
    $success = $stmt->bind_param(
        "iiiiii",
        $holdID,                    // cancelled hold is just the row with cancelled hold
        $holdInQueue,               // only target holds in waiting
        $holdID,                    // need to reread from original hold table in sub query
        $activeHold,                // Set hold status to active now that we have a copy
        $onHold,                    // change book status to ON_HOLD
        $bookAvailable              // Make sure book is available
    );
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, "reserveHoldCopy");
    }

    return $success;
}