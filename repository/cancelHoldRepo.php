<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';

/* Cancel the hold - change hold status, decrease hold count by one */
function cancelHold($holdID, $holderID, $conn, $debug = false)
{
    $query =
        '
            UPDATE holds 
                LEFT OUTER JOIN bookItem ON holds.reservedCopy = bookItem.bookBarcode
                INNER JOIN books ON holds.bookISBN = books.bookISBN
            SET
                holds.status = :holdCancelled,
                books.holds = books.holds - 1,
                bookItem.status = :bookAvailable
            WHERE
                holds.holdID = :holdID
                AND holds.holderID = :holderID
                AND (
                        holds.status = :holdActive
                        OR holds.status = :holdInQueue
                    ) 

        ';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':holdCancelled', HOLD_CANCELLED, PDO::PARAM_INT);
    $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT);
    $stmt->bindValue(':holdID', $holdID, PDO::PARAM_INT);
    $stmt->bindValue(':holderID', $holderID, PDO::PARAM_INT);
    $stmt->bindValue(':holdActive', HOLD_ACTIVE, PDO::PARAM_INT);
    $stmt->bindValue(':holdInQueue', HOLD_IN_QUEUE, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);

    return $numRows === 3;
}

/* Reserve newly freed copy to user next in line for holds */
function reserveHoldCopy($holdID, $conn, $debug = false)
{
    $query =
        '
           UPDATE
               (
               SELECT
                   *
               FROM
                   holds
               WHERE
                   holdID = :canceledHoldID
           ) AS cancelledHold
           INNER JOIN (
               SELECT *
               FROM
                   holds AS h
               LEFT OUTER JOIN courseEnrolled ON courseEnrolled.studentID = h.holderID
               WHERE
                    h.status = :holdInQueue
                    AND h.bookISBN IN (SELECT bookISBN FROM holds WHERE holdID = :holdID )
               ORDER BY
                   TIMESTAMP
               LIMIT 1
           ) AS filteredHold
           INNER JOIN bookItem ON bookItem.bookBarcode = cancelledHold.reservedCopy
           INNER JOIN holds ON filteredHold.holdID = holds.holdID
           SET
               holds.status = :holdActive,
               holds.reservedCopy = cancelledHold.reservedCopy,
               holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
               bookItem.status = :onHold
           WHERE
               bookItem.status = :bookAvailable
        ';
    $stmt = $conn->prepare($query);
    $success =
        // cancelled hold is just the row with cancelled hold
        $stmt->bindValue(':canceledHoldID', $holdID, PDO::PARAM_INT) &&
        // only target holds in waiting
        $stmt->bindValue(':holdInQueue', HOLD_IN_QUEUE, PDO::PARAM_INT) &&
        // need to reread from original hold table in sub query
        $stmt->bindValue(':holdID', $holdID, PDO::PARAM_INT) &&
        // Set hold status to active now that we have a copy
        $stmt->bindValue(':holdActive', HOLD_ACTIVE, PDO::PARAM_INT) &&
        // change book status to ON_HOLD
        $stmt->bindValue(':onHold', BOOK_ON_HOLD, PDO::PARAM_INT) &&
        // Make sure book is available
        $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'reserveHoldCopy');
    }

    return $success;
}