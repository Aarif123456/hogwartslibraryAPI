<?php

function librarianListJSON($listType, $conn)
{
    switch ($listType) {
        case 'loadPotentialBorrower':
            $stmt = $conn->prepare(
                '
                SELECT 
                    memberID,fname,lname 
                FROM 
                    members 
                WHERE 
                    memberID != :id 
                    AND memberID NOT IN 
                        (
                            SELECT 
                                headmasterID AS memberID 
                            FROM 
                                headmasters
                        )'
            );
            $stmt->bindValue(':id', getUserID($conn), PDO::PARAM_INT);

            return $stmt;
        case 'loadFinedMember':
            $stmt = $conn->prepare(
                '
                SELECT 
                    memberID,fname,lname,fines 
                FROM 
                    members 
                WHERE 
                    memberID != :id 
                    AND memberID>0 AND fines>0'
            );
            $stmt->bindValue(':id', getUserID($conn), PDO::PARAM_INT);

            return $stmt;
        case 'loadBookBarcode':
            $stmt = $conn->prepare(
                '
                SELECT 
                    * 
                FROM 
                    books NATURAL JOIN bookItem 
                WHERE 
                    bookBarcode LIKE CONCAT(\'%\',:bookBarcode,\'%\')'
            );
            $bookBarcode = $_POST['bookBarcode'] ?? 'NONE';
            $stmt->bindValue(':bookBarcode', $bookBarcode, PDO::PARAM_STR);

            return $stmt;

        default:
    }

    return null;
}


