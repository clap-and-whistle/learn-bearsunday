<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\Page;

use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class NextTest extends TestCase
{
    private ResourceInterface $resource;
    private string $linkKey = 'index';
    private string $hrefStr;

    protected function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);

        $this->hrefStr = "/{$this->linkKey}";
    }

    public function testOnGet(): void
    {
        // 実行
        $ro = $this->resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $this->assertSame(200, $ro->code);
        $this->assertStringContainsString('Mon', $ro->toString());
        $this->assertArrayHasKey($this->linkKey, $ro->body['_links']);
        $this->assertStringContainsString($this->hrefStr, $ro->body['_links'][$this->linkKey]['href']);
    }

    public function testOnGetHtml(): void
    {
        // 準備
        $injector = Injector::getInstance('html-app');
        $resource = $injector->getInstance(ResourceInterface::class);

        // 実行
        $ro = $resource->get('page://self/next', ['year' => 2001, 'month' => 1, 'day' => 1]);

        // 検証
        $this->assertSame(200, $ro->code);

        $htmlContents = $ro->toString();
        $this->assertNotEmpty($htmlContents);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlContents);
        $element = $dom->getElementById('link_' . $this->linkKey);
        $this->assertSame('index page', $element?->nodeValue);
        $this->assertSame('a', $element?->tagName);
        $this->assertSame($this->hrefStr, $element?->getAttribute('href'));
    }
}
