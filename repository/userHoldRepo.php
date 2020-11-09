<?php
/* Code for queries that will be used in the menu's about the user */

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

function getUserHoldsResults($userID, $conn)
{
    // get query
    $query = "SELECT * FROM `holds` NATURAL JOIN `books` WHERE " .
             "holderID = ? AND (holds.status=" . HOLD_ACTIVE .
             " OR holds.status=" . HOLD_IN_QUEUE . ")";
    $holdStmt = $conn->prepare($query);
    $holdStmt->bind_param("i", $userID);
    // if no query return back null
    if (empty($holdStmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($holdStmt);
}

function holdExists($bookISBN, $holderID, $conn)
{
    $holdStmt = $conn->prepare("SELECT * FROM holds WHERE bookISBN =? AND holderID =?");
    $holdStmt->bind_param("si", $bookISBN, $holderID);
    $holdStmt->execute();
    $holdResult = $holdStmt->get_result();

    return $holdResult->num_rows != 0;
}


function createHold($bookISBN, $holderID, $conn, $debug = false): bool
{
    $stmt = $conn->prepare(
        "INSERT INTO holds (bookISBN, holderID, holdExpiryDate) VALUES (?, ?, DATE_ADD(CURDATE(), INTERVAL 1 YEAR))"
    );
    $stmt->bind_param("si", $bookISBN, $holderID);

    return safeWriteQueries($stmt, $conn, $debug) && updateBooktable($bookISBN, $conn, $debug);
}

function updateBookTable($bookISBN, $conn, $debug): bool
{
    $stmt = $conn->prepare(
        "UPDATE books SET holds=holds+1 WHERE bookISBN=?"
    );
    $stmt->bind_param("s", $bookISBN);

    return safeWriteQueries($stmt, $conn, $debug);
}

/* Go through hold table and update book */
function reserveCopy($bookISBN, $holderID, $courseID, $conn)
{
    $stmt = $courseID ? getReserveCopyForCourse($bookISBN, $holderID, $courseID, $conn)
        : getReserveCopy($bookISBN, $holderID, $conn);

    return $stmt->execute() && !empty($conn->affected_rows);
}

function getReserveCopyForCourse($bookISBN, $holderID, $courseID, $conn)
{
    $stmt = $conn->prepare(
        "
            UPDATE
                (
                    SELECT
                     *
                    FROM
                     bookItem
                WHERE
                    bookItem.bookISBN = ?
                    AND `status` = ?
                    AND reservedFor =?
                    LIMIT 1
                ) AS reservedCopies
                INNER JOIN holds ON holds.bookISBN = reservedCopies.bookISBN
                INNER JOIN bookItem ON bookItem.bookBarcode = reservedCopies.bookBarcode
            SET
                holds.reservedCopy = reservedCopies.bookBarcode,
                holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
                holds.status = ?,
                bookItem.status = ?
            WHERE
                reservedCopies.bookISBN = ?
                AND holderID = ?
                AND holds.status = ?
        "
    );
    $bookAvailable = BOOK_AVAILABLE;
    $holdActive = HOLD_ACTIVE;
    $holdInQueue = HOLD_IN_QUEUE;
    $onHold = BOOK_ON_HOLD;
    $stmt->bind_param(
        "siiiisii",
        $bookISBN,
        $bookAvailable,
        $courseID,
        $holdActive,
        $onHold,
        $bookISBN,
        $holderID,
        $holdInQueue
    );

    return $stmt;
}

function getReserveCopy($bookISBN, $holderID, $conn)
{
    $stmt = $conn->prepare(
        "
            UPDATE
                (
                    SELECT
                     *
                    FROM
                     bookItem
                WHERE
                    bookItem.bookISBN = ?
                    AND `status` = ?
                    AND reservedFor IS NULL
                    LIMIT 1
                ) AS reservedCopies
                INNER JOIN holds ON holds.bookISBN = reservedCopies.bookISBN
                INNER JOIN bookItem ON bookItem.bookBarcode = reservedCopies.bookBarcode
            SET
                holds.reservedCopy = reservedCopies.bookBarcode,
                holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
                holds.status = ?,
                bookItem.status = ?
            WHERE
                reservedCopies.bookISBN = ?
                AND holderID = ?
                AND holds.status = ?
        "
    );
    $bookAvailable = BOOK_AVAILABLE;
    $holdActive = HOLD_ACTIVE;
    $holdInQueue = HOLD_IN_QUEUE;
    $onHold = BOOK_ON_HOLD;
    $stmt->bind_param(
        "siiisii",
        $bookISBN,
        $bookAvailable,
        $holdActive,
        $onHold,
        $bookISBN,
        $holderID,
        $holdInQueue
    );

    return $stmt;
}