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

// note: ./var/tmp/ を空っぽにした直後の状態で composer test するとなぜか束縛の差し替えが期待通り動かないが、
//       連続して2回目の composer test を実行すると束縛の差し替えが効くようになる（この挙動の原因の究明はできていない）。
//       そこで、この setup.php の中で1回目の「失敗する composer test」を済ませておくことにしたのが下記の行。
echo '    phpunit を実行しています（結果の出力内容はすべて捨てられます）' . PHP_EOL;
passthru('php -dextension=pcov.so -d pcov.enabled=1 ./vendor/bin/phpunit --stderr 2>/dev/null');
passthru('rm ./.phpunit.result.cache');

// プロジェクトルートに autoload.php が無ければ生成
chdir(dirname(__DIR__));
if (! file_exists('./autoload.php')) {
    echo '    プロジェクトルートへ autoload.php を作成しています: ./autoload.php' . PHP_EOL;
    passthru('./vendor/bin/bear.compile \'Cw\LearnBear\' prod-app ./ 2>/dev/null');
}

echo 'end setup.' . PHP_EOL;
