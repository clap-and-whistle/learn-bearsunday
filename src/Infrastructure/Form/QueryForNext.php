<?php

declare(strict_types=1);

namespace Cw\LearnBear\Infrastructure\Form;

use DateTime;

/**
 * Polidog.Todoに必要なものではありませんが、
 * 「標準チュートリアルに認証用AOPを追加した上でPolidog.Todoを被せる」という趣旨により標準チュートリアル部分の実装例をあえて残してあるので、
 * その対応に重複するコードをまとめただけのtraitです。
 */
trait QueryForNext
{
    private function getQueryStrForNext(): string
    {
        $now = new DateTime();
        $year = $now->format('Y');
        $month = $now->format('n');
        $day = $now->format('j');

        return "?year={$year}&month={$month}&day={$day}";
    }
}
