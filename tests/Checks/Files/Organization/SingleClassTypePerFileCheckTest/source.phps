<?php

interface Foo {}

trait Bar {
	public function createAnonymousClass()
	{
		return new class {};
	}
}

class Baz {}
