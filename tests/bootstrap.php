<?php declare(strict_types = 1);

namespace Stylist\Tests;

use Tester\Environment;
use Tester\Helpers;


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/checks.php';

Environment::setup();
Environment::bypassFinals();
\date_default_timezone_set('UTC');

\define('Stylist\\Tests\\TEMP_DIR', __DIR__ . '/temp/' . (isset($_SERVER['argv']) ? \md5(\serialize($_SERVER['argv'])) : \getmypid()));
Helpers::purge(TEMP_DIR);
