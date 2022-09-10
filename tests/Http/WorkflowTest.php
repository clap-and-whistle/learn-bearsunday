<?php

declare(strict_types=1);

namespace Cw\LearnBear\Http;

use BEAR\Dev\Http\HttpResource;
use BEAR\Resource\Code;
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
    private function getLinkUrlFromAtag(string $roStr, string $linkId): array
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

    public function testIndex(): string
    {
        // 実行
        $indexRo = $this->resource->get('/');

        // 検証
        $this->assertSame(Code::OK, $indexRo->code);
        $html = $indexRo->toString();
        $this->assertNotEmpty($html);

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $formElement = $dom->getElementById('login-form');
        $this->assertNotNull($formElement);

        return $formElement->getAttribute('action');
    }

    /**
     * @depends testIndex
     */
    public function testLoginAllow(string $requestPath): string
    {
        // 準備
        $inputUsername = 'hogetest';
        $inputPassword = 'Fuga1234';

        // 実行
        $loginRo = $this->resource->post($requestPath, ['username' => $inputUsername, 'password' => $inputPassword]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $loginRo->code);
        $redirectHtml = $loginRo->toString();
        [$path, $queryStr] = $this->getLinkUrlFromAtag($redirectHtml, 'redirect-to');

        return $path . '?' . $queryStr;
    }

    /**
     * @depends testLoginAllow
     */
    public function testNext(string $requestPath): string
    {
        // 準備
        [$path, $queryStr] = explode('?', $requestPath);
        $queryArray = [];
        if ($queryStr) {
            parse_str($queryStr, $queryArray);
        }

        // 実行
        $nextRo = $this->resource->get($path, $queryArray);

        // 検証
        $this->assertSame(Code::OK, $nextRo->code);
        $html = $nextRo->toString();
        $this->assertNotEmpty($html);

        [$path] = $this->getLinkUrlFromAtag($html, 'link_logout');

        return $path;
    }

    /**
     * @depends testNext
     */
    public function testLogout(string $requestPath): string
    {
        // 実行
        $logoutRo = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $logoutRo->code);
        $html = $logoutRo->toString();
        $this->assertNotEmpty($html);

        [$path] = $this->getLinkUrlFromAtag($html, 'link_index');

        return $path;
    }

    /**
     * @depends testLogout
     */
    public function testReturnIndex(string $requestPath): ResourceObject
    {
        // 実行
        $indexRo = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $indexRo->code);
        $html = $indexRo->toString();
        $this->assertNotEmpty($html);

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $element = $dom->getElementById('starting-point');
        $this->assertNotNull($element);
        $this->assertSame('h1', $element->tagName);
        $this->assertSame('index', $element->textContent);

        return $indexRo;
    }
}
