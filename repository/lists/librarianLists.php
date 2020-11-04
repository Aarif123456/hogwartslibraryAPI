<?php

function librarianListJSON($listType, $conn)
{
    $stmt = null;
    switch ($listType) {
        case 'loadPotentialBorrower':
            $stmt = $conn->prepare(
                "SELECT memberID,fname,lname FROM members WHERE memberID !=? AND memberID NOT IN (SELECT headmasterID AS memberID FROM headmasters)"
            );
            $stmt->bind_param("i", $_SESSION['userID']);

            return $stmt;
        case 'loadFinedMember':
            $stmt = $conn->prepare(
                "SELECT memberID,fname,lname,fines FROM members WHERE memberID !=? AND memberID>0 AND fines>0"
            );
            $stmt->bind_param("i", $_SESSION['userID']);

            return $stmt;
        case 'loadBookBarcode':
            if (isValidPostVar('bookBarcode')) {
                $stmt = $conn->prepare(
                    "SELECT * FROM books NATURAL JOIN bookItem WHERE bookBarcode LIKE CONCAT('%',?,'%')"
                );
                $stmt->bind_param("s", $_POST['bookBarcode']);
                return $stmt;
            }
            break;
        default:
    }
    return null;
}


