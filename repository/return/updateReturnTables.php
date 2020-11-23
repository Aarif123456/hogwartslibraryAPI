<?php

/* Imports */
require_once __DIR__ . '/../error.php';
require_once __DIR__ . '/../statusConstants.php';


function updateReturnInfo($parameter, $conn, $debug = false)
{
    $todayDate = $parameter->todayDate;
    $librarianID = $parameter->librarianID;
    $bookBarcode = $parameter->bookBarcode;
    $stmt = $conn->prepare(
        'UPDATE members, bookItem, transactions
                            SET bookItem.status = :bookAvailable,
                                transactions.returnerID = :librarianID,
                                transactions.returnDate = DATE(:todayDate),
                                members.fines = members.fines + transactions.fine,
                                members.numBooks = members.numBooks - 1, 
                                members.status = IF (
                                                        (
                                                            (
                                                                members.fines >= :studentLimit 
                                                                AND members.memberID IN 
                                                                (
                                                                    SELECT studentID AS memberID FROM students
                                                                )
                                                            ) OR 
                                                            (
                                                                members.fines >= :professorLimit 
                                                                AND members.memberID IN 
                                                                (
                                                                    SELECT professorID AS memberID FROM professor
                                                                )
                                                            )
                                                        ), 
                                                        :blacklisted, 
                                                        members.status 
                                                    )
                                WHERE transactions.returnDate IS NULL 
                                    AND transactions.bookBarcode = :bookBarcode
                                    AND bookItem.bookBarcode = transactions.bookBarcode
                                    AND members.memberID = transactions.borrowedBy 
                                    AND bookItem.status = :checkedOut
                '
    );

    $success = $stmt->bindValue(':bookAvailable', BOOK_AVAILABLE, PDO::PARAM_INT) &&
               $stmt->bindValue(':librarianID', $librarianID, PDO::PARAM_INT) &&
               $stmt->bindValue(':todayDate', $todayDate, PDO::PARAM_STR) &&
               $stmt->bindValue(':studentLimit', STUDENT_BOOK_LIMIT, PDO::PARAM_INT) &&
               $stmt->bindValue(':professorLimit', PROFESSOR_BOOK_LIMIT, PDO::PARAM_INT) &&
               $stmt->bindValue(':blacklisted', ACCOUNT_BLACKLISTED, PDO::PARAM_INT) &&
               $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_INT) &&
               $stmt->bindValue(':checkedOut', BOOK_CHECKED_OUT, PDO::PARAM_INT);
    $numRows = safeUpdateQueries($stmt, $conn, $debug);
    if ($debug) {
        echo debugQuery($numRows, $success, 'updateReturnInfo');
    }

    return $numRows === 3 && $success;
}