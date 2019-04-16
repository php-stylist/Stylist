<?php

function answer(): int {
	return 42;
}

$answer = answer();
if ($answer < 42) {
    $result = true;
} elseif ($answer > 42) {
   $result = false;
} else {
	$result = null;
}
