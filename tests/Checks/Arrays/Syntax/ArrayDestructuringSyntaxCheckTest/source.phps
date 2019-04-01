<?php

$array = [1, 2, 3];

// short syntax
[$a,, $c] = $array;

// foreach
foreach ($array as [$a, $b]) {}

// long syntax
list($a,, $c) = $array;
