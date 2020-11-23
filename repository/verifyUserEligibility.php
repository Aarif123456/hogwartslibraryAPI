<?php
//verify if user can take out book
/* Imports */
require_once __DIR__ . '/error.php';

/* Return true if user is blacklisted  */
function checkUserBlacklisted($userID, $conn)
{
    $stmt = $conn->prepare('SELECT memberID FROM members WHERE memberID=:id and status=:blacklisted');
    $stmt->bindValue(':id', $userID, PDO::PARAM_INT);
    $stmt->bindValue(':blacklisted', ACCOUNT_BLACKLISTED, PDO::PARAM_INT);

    return !(empty(count(getExecutedResult($stmt))));
}

