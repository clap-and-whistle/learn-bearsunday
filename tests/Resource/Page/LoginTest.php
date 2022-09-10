<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use DateTime;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private ResourceInterface $resource;
    private string $linkKey = 'next';
    private string $expectedRedirectTo;

    public function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);

        $now = new DateTime();
        $expectedQueryStr =
            'year=' . $now->format('Y')     // 年。4 桁の数字。
            . '&month=' . $now->format('n')   // 月。数字。先頭にゼロをつけない。
            . '&day=' . $now->format('j');    // 日。先頭にゼロをつけない。
        $this->expectedRedirectTo = "/{$this->linkKey}?" . $expectedQueryStr;
    }

    public function testOnPost(): void
    {
        // 準備
        $username = 'hogetest';
        $password = 'Fuga1234';

        // 実行
        $ro = $this->resource->post('page://self/login', ['username' => $username, 'password' => $password]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $ro->code);
        $actualRedirectTo = $ro->body['_links']['redirect']['href'];
        $this->assertSame($this->expectedRedirectTo, $actualRedirectTo);
    }

    public function testOnPostHtml(): void
    {
        // 準備
        $username = 'piyotest';
        $password = 'Fuga5678';

        $injector = Injector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->post('page://self/login', ['username' => $username, 'password' => $password]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $metas = $dom->getElementsByTagName('meta');
        $this->assertTrue((bool) $metas->count());
        foreach ($metas as $meta) {
            $content = $meta->getAttribute('content');
            if (! $content) {
                continue;
            }

            $this->assertStringContainsString($this->expectedRedirectTo, $content, 'METAタグのリダイレクト先が期待値と異なります');
        }

        $aTag = $dom->getElementById('redirect-to');
        $this->assertNotNull($aTag, 'NextページへジャンプするAタグの記述がありません');
        $this->assertSame($this->expectedRedirectTo, $aTag->getAttribute('href'), 'リンク先が期待値と異なります');
    }
}
