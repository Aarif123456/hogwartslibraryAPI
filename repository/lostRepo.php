<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';

/* report book as lost
        1. change bookItem status to lost
        2. Remove reservation
        3. Charge user the appropriate amount
        4. Decrement books checked out to user by one 
*/
function reportLostBook($userID, $bookBarcode, $conn, $debug = false)
{
    $query =
        '
        UPDATE 
            transactions
            INNER JOIN bookItem  
                ON transactions.bookBarcode=bookItem.bookBarcode
            INNER JOIN books
                ON books.bookISBN=bookItem.bookISBN
            INNER JOIN members  
                ON transactions.borrowedBy=members.memberID
        SET 
            bookItem.status = :lostBook,
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
                                            ) >=:studentLimit
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
                                            ) >=:professorLimit
                                            AND members.memberID IN 
                                            (
                                                SELECT professorID AS memberID FROM professor
                                            )
                                        )
                                    ), 
                                    :blacklisted, 
                                    members.status 
                                )
        WHERE 
            transactions.bookBarcode = :bookBarcode 
            AND transactions.borrowedBy = :userID
            AND transactions.returnDate IS NULL
            AND bookItem.status = :checkedOut
            ';
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':lostBook', BOOK_LOST, PDO::PARAM_INT);
    $stmt->bindValue(':studentLimit', STUDENT_BOOK_LIMIT, PDO::PARAM_INT);
    $stmt->bindValue(':professorLimit', PROFESSOR_BOOK_LIMIT, PDO::PARAM_INT);
    $stmt->bindValue(':blacklisted', ACCOUNT_BLACKLISTED, PDO::PARAM_INT);
    $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT);
    $stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':checkedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);

    return $numRows === 3;
}
