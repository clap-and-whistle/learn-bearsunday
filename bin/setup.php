<?php

declare(strict_types=1);

chdir(dirname(__DIR__));
passthru('rm -rf ./var/tmp/*');

// SQLiteのデータベースが無ければ作成
if (! file_exists(dirname(__DIR__) . '/var/db/todo.sqlite3')) {
    echo '    SQLiteのデータベースファイルを作成しています: ./var/db/todo.sqlite3' . PHP_EOL;
    chdir(dirname(__DIR__) . '/var/db');
    passthru('sqlite3 todo.sqlite3 < todo.sql');
    chdir(dirname(__DIR__));
}

// プロジェクトルートに autoload.php が無ければ生成
chdir(dirname(__DIR__));
if (! file_exists('./autoload.php')) {
    echo '    プロジェクトルートへ autoload.php を作成しています: ./autoload.php' . PHP_EOL;
    passthru('./vendor/bin/bear.compile \'Cw\LearnBear\' prod-app ./ 2>/dev/null');
}

echo 'end setup.' . PHP_EOL;
