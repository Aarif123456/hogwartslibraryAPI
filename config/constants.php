<?php

define('REGISTRATION_PASSCODE', '12345');

/* Some users have default chart that can be changed safely to show in dashboard */
$defaultChart = [
    'student' => 'numBooksPerCategory',
    'professor' => 'activityPerMajorAndDepartment'
];

define('MAXIMUM_COPIES_RESERVED', 10);