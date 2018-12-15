<?php

// short array syntax is ok
$foo = [42, 'foo'];

// array cast is ok
$bar = (array) $foo;

// array as a type hint is ok
$f = function (array $foo) {};

// array as a return type is ok
function foo(): array {}

// array() is not ok
$baz = array(42, 'foo');
