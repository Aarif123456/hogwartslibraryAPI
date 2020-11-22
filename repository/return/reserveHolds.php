<?php

/* Imports */
require_once  __DIR__ .'/../error.php';
require_once  __DIR__ .'/../statusConstants.php';


/* When the book get returned we check the holds table to see if the book has any holds, if it does we reserve this copy to 
*   the user first in line.
* We also need to factor in reservation, so if the book has been reserved we will look for user's in the course before reserving 
*/

function reserveCopyForHolds($parameter, $conn, $debug = false)
{
    $bookBarcode = $parameter->bookBarcode;
    $query =
        '
                UPDATE
                (
                    SELECT *
                        FROM holds as h WHERE
                            h.status = :inQueue
                            AND h.bookISBN IN 
                            (
                                SELECT bookISBN
                                    FROM bookItem AS BK WHERE 
                                        bookBarcode = :bookBarcodeReserve
                                        AND 
                                        (
                                            BK.reservedFor IS NULL
                                            OR BK.reservedFor IN 
                                            (
                                                SELECT courseID AS reservedFor
                                                    FROM courseEnrolled WHERE
                                                        studentID = h.holderID
                                            )
                                        )
                            )
                        ORDER BY timeStamp
                        LIMIT 1
                ) AS filteredHold 
                    INNER JOIN bookItem ON bookItem.bookISBN = filteredHold.bookISBN
                    INNER JOIN holds ON filteredHold.holdID = holds.holdID
                    SET
                            holds.status = :activeHold,
                            holds.reservedCopy = :reserveCopyBarcode,
                            holds.holdExpiryDate = DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
                            bookItem.status = :onHold
                    WHERE
                    bookItem.bookBarcode = :bookBarcode
                    AND bookItem.status = :bookAvailable
            ';
    $stmt = $conn->prepare($query);
    // only target holds in waiting           
    $success = $stmt->bindValue(':inQueue', HOLD_IN_QUEUE, PDO::PARAM_INT) && // only target holds in waiting
               // primary key in bookItem used to handle reservation
               $stmt->bindValue(':bookBarcodeReserve', $bookBarcode, PDO::PARAM_INT) &&
               // Set hold status to active now that we have a copy
               $stmt->bindValue(':activeHold', HOLD_ACTIVE, PDO::PARAM_INT) &&
               // reserve the copy for the hold
               $stmt->bindValue(':reserveCopyBarcode', $bookBarcode, PDO::PARAM_INT) &&
               // change book status to ON_HOLD
               $stmt->bindValue(':onHold', BOOK_ON_HOLD, PDO::PARAM_INT) &&
               // make sure we only get one element from bookItem
               $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT) &&
               // Make sure book is available
               $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT);

    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'reserveCopyForHolds');
    }

    return $success;
}