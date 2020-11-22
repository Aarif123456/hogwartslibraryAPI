<?php

/* Imports */
require_once  __DIR__ .'/../error.php';
require_once  __DIR__ .'/../statusConstants.php';
require_once  __DIR__ .'/updateReturnTables.php';
/* If lost book is found then return a portion of the money and mark book as available */
function refundLostBook($parameter, $conn, $debug = false)
{
    $librarianID = $parameter->librarianID;
    $bookBarcode = $parameter->bookBarcode;
    // TODO If this moves user out of blacklist zone then change status
    $query = '
            UPDATE 
                bookItem, 
                members, 
                transactions, 
                books
            SET 
                members.fines = members.fines-bookItem.price* :percentRefunded,
                bookItem.status = :bookAvailable,
                books.totalCopies =  books.totalCopies + 1,
                transactions.returnDate = CURDATE(),
                transactions.returnerID = :librarianID
            WHERE 
                bookItem.status = :bookLost 
                AND bookItem.bookBarcode = :bookBarcode
                AND transactions.bookBarcode=bookItem.bookBarcode
                AND transactions.returnDate IS NULL
                AND transactions.borrowedBy = members.memberID
                AND bookItem.bookISBN = books.bookISBN
            ';
    $stmt = $conn->prepare($query);
    $success = $stmt->bindValue(':percentRefunded', strval(REFUND_FOR_FOUND_BOOK), PDO::PARAM_STR) &&
               $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT) &&
               $stmt->bindValue(':librarianID', $librarianID, PDO::PARAM_INT) &&
               $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT) &&
               $stmt->bindValue(':bookLost', BOOK_LOST, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'refundLostBook');
    }

    return $success && $numRows === 4 && reserveCopyForHolds($bookBarcode, $conn, $debug);
}