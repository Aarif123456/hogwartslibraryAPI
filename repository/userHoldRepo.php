<?php

/* Imports */
require_once __DIR__ . '/error.php';
require_once __DIR__ . '/statusConstants.php';
require_once __DIR__ . '/holds/holdsMenu.php';
require_once __DIR__ . '/holds/reserveHoldCopy.php';
require_once __DIR__ . '/holds/createHolds.php';

function holdExists($bookISBN, $holderID, $conn)
{
    $stmt = $conn->prepare('SELECT * FROM holds WHERE bookISBN = :bookISBN AND holderID = :id');
    $stmt->bindValue(':id', $holderID, PDO::PARAM_INT);
    $stmt->bindValue(':bookISBN', $bookISBN, PDO::PARAM_STR);
    $holdResult = getExecutedResult($stmt);

    return (int)count($holdResult) !== 0;
}
