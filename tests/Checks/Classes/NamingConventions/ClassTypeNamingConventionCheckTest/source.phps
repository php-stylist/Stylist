<?php

interface foo {}

trait Bar {}

class bar_baz {}

class FOO_BAR extends bar_baz implements foo {
	use Bar;
}
