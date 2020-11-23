<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


function getUserCheckedOutBooks($userID, $conn)
{
    // get query
    $stmt = $conn->prepare(
        'SELECT 
            bookName,
            author,
            bookBarcode,
            dueDate,
            renewedTime, 
            holds 
        FROM 
            `transactions` 
            NATURAL JOIN `bookItem` 
            NATURAL JOIN `books`
        WHERE 
            transactions.borrowedBy = :id 
            AND transactions.returnDate IS NULL 
            AND bookItem.status = :checkedOut
        '
    );
    $stmt->bindValue(':id', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':checkedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT);
    // if no query return back null
    if (empty($stmt)) {
        return null;
    }

    // otherwise return back result from query
    return getExecutedResult($stmt);
}