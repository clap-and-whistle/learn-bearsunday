<?php

declare(strict_types=1);

namespace Cw\LearnBear\Http;

use BEAR\Dev\Http\HttpResource;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\Hypermedia\WorkflowTest as Workflow;
use DOMDocument;
use RuntimeException;

use function explode;
use function htmlspecialchars_decode;
use function parse_str;

class WorkflowTest extends Workflow
{
    protected function setUp(): void
    {
        $this->resource = new HttpResource('127.0.0.1:8080', __DIR__ . '/index.php', __DIR__ . '/log/workflow.log');
    }

    /**
     * @return array{path: string, queryStr?: string}
     * @psalm-return non-empty-list<string>
     */
    private function getLinkUrl(string $roStr, string $linkId): array
    {
        if (empty($roStr)) {
            throw new RuntimeException('empty string');
        }

        $dom = new DOMDocument();
        $dom->loadHTML($roStr);
        $href = $dom->getElementById($linkId)?->getAttribute('href');

        return $href
            ? explode('?', htmlspecialchars_decode($href))
            : throw new RuntimeException("There is no link: {$linkId}");
    }

    /**
     * @depends testIndex
     */
    public function testNext(ResourceObject $index): ResourceObject
    {
        [$path, $queryStr] = $this->getLinkUrl($index->toString(), 'link_next');
        $queryArray = [];
        if ($queryStr) {
            parse_str($queryStr, $queryArray);
        }

        $next = $this->resource->get($path, $queryArray);
        $this->assertSame(200, $next->code);

        return $next;
    }
}
