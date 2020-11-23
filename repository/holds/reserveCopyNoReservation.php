<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';

function getReserveCopy($bookISBN, $holderID, $conn)
{
    $stmt = $conn->prepare(
        '
            UPDATE
                (
                    SELECT
                     *
                    FROM
                     bookItem
                WHERE
                    bookItem.bookISBN = :bookISBN
                    AND `status` = :bookAvailable
                    AND reservedFor IS NULL
                    LIMIT 1
                ) AS reservedCopies
                INNER JOIN holds ON holds.bookISBN = reservedCopies.bookISBN
                INNER JOIN bookItem ON bookItem.bookBarcode = reservedCopies.bookBarcode
            SET
                holds.reservedCopy = reservedCopies.bookBarcode,
                holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
                holds.status = :activeHold,
                bookItem.status = :onHold
            WHERE
                reservedCopies.bookISBN = :reserveBookISBN
                AND holderID = :id
                AND holds.status = :inQueue
        '
    );
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);
    $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT);
    $stmt->bindValue(':activeHold', HOLD_ACTIVE, PDO::PARAM_INT);
    $stmt->bindValue(':onHold', BOOK_ON_HOLD, PDO::PARAM_INT);
    $stmt->bindValue(':reserveBookISBN', $bookISBN, PDO::PARAM_STR);
    $stmt->bindValue(':id', $holderID, PDO::PARAM_INT);
    $stmt->bindValue(':inQueue', HOLD_IN_QUEUE, PDO::PARAM_INT);

    return $stmt;
}