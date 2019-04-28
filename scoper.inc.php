<?php declare(strict_types = 1);

return [
	'prefix' => null,
	'finders' => [],
	'patchers' => [
		static function (string $filePath, string $prefix, string $content): string {
			if (strpos($filePath, 'vendor/nette/di/src/DI/Extensions/ParametersExtension.php') !== false) {
				return preg_replace(
					'/addBody\(\'Nette\\\\\\\\Utils\\\\\\\\Validators/',
					'addBody(\'' . $prefix . '\\\\\\\\Nette\\\\\\\\Utils\\\\\\\\Validators',
					$content
				);
			}

			return $content;
		},
	],
	'whitelist' => [
		'Stylist\*',
	],
];
