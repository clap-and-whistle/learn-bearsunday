<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use DateTime;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private ResourceInterface $resource;
    private string $linkKey = 'next';
    private string $hrefStr;

    protected function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);

        $now = new DateTime();
        $expectedQueryStr =
              'year=' . $now->format('Y')     // 年。4 桁の数字。
            . '&month=' . $now->format('n')   // 月。数字。先頭にゼロをつけない。
            . '&day=' . $now->format('j');    // 日。先頭にゼロをつけない。
        $this->hrefStr = "/{$this->linkKey}?" . $expectedQueryStr;
    }

    public function testOnGet(): void
    {
        // 実行
        $ro = $this->resource->get('page://self/index');

        // 検証
        $this->assertSame(200, $ro->code);
        $this->assertArrayHasKey($this->linkKey, $ro->body['_links']);
        $this->assertStringContainsString($this->hrefStr, $ro->body['_links'][$this->linkKey]['href']);
    }

    public function testOnGetHtml(): void
    {
        // 準備
        $injector = Injector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/index');

        // 検証
        $this->assertSame(200, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $element = $dom->getElementById('link_' . $this->linkKey);
        $this->assertSame('next page', $element?->nodeValue);
        $this->assertSame('a', $element?->tagName);
        $this->assertSame($this->hrefStr, $element?->getAttribute('href'));
    }
}
