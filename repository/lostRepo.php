<?php

/* Imports */
require_once 'error.php';
require_once 'statusConstants.php';

/* report book as lost
        1. change bookItem status to lost
        2. Remove reservation
        3. Charge user the appropriate amount
        4. Decrement books checked out to user by one 
*/
function reportLostBook($userID, $bookBarcode, $conn, $debug = false)
{
    $query =
        "
        UPDATE 
            transactions
            INNER JOIN bookItem  
                ON transactions.bookBarcode=bookItem.bookBarcode
            INNER JOIN books
                ON books.bookISBN=bookItem.bookISBN
            INNER JOIN members  
                ON transactions.borrowedBy=members.memberID
        SET 
            bookItem.status = ?,
            bookItem.reservedFor = NULL,
            books.totalCopies = books.totalCopies - 1,
            members.fines = (members.fines + transactions.fine + bookItem.price),
            members.numBooks = members.numBooks - 1, 
            members.status = IF 
                                (
                                    (
                                        (
                                            (
                                                members.fines + 
                                                transactions.fine + 
                                                bookItem.price
                                            ) >=? 
                                            AND members.memberID IN 
                                            (
                                                SELECT studentID AS memberID FROM students
                                            )
                                        ) OR 
                                        (
                                            (
                                                members.fines + 
                                                transactions.fine + 
                                                bookItem.price
                                            ) >=? 
                                            AND members.memberID IN 
                                            (
                                                SELECT professorID AS memberID FROM professor
                                            )
                                        )
                                    ), 
                                    ?, 
                                    members.status 
                                )
        WHERE 
            transactions.bookBarcode = ? 
            AND transactions.borrowedBy = ?
            AND transactions.returnDate IS NULL
            AND bookItem.status = ? 
            ";
    $lostBook = BOOK_LOST;
    $bookCheckedOut = BOOK_CHECKED_OUT;
    $blacklisted = ACCOUNT_BLACKLISTED;
    $studentLimit = STUDENT_BOOK_LIMIT;
    $professorLimit = PROFESSOR_BOOK_LIMIT;
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "iiiiiii",
        $lostBook,
        $studentLimit,
        $professorLimit,
        $blacklisted,
        $bookBarcode,
        $userID,
        $bookCheckedOut
    );
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    echo $conn->affected_rows;
    echo '  ';
    echo $numRows;
    echo '  ';

    return $numRows === 3;
}
