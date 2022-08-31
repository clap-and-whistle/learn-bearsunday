<?php

declare(strict_types=1);

use Cw\LearnBear\Bootstrap;

require dirname(__DIR__, 2) . '/vendor/autoload.php';
exit((new Bootstrap())('html-app', $GLOBALS, $_SERVER));
