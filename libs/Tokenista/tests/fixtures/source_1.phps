<?php

$foo = 42;
if ($foo < 42) {
	@file_put_contents('data.' . $foo . '.txt', $foo + 5);
}
