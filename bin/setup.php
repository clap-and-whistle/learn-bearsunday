<?php

declare(strict_types=1);

chdir(dirname(__DIR__));
passthru('rm -rf ./var/tmp/*');

passthru('php -dextension=pcov.so -d pcov.enabled=1 ./vendor/bin/phpunit --stderr 2>/dev/null');
