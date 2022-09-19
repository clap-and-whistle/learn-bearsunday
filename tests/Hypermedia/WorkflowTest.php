<?php

declare(strict_types=1);

namespace Cw\LearnBear\Hypermedia;

use BEAR\Resource\Code;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\AppSpi\SessionHandlerInterface;
use Cw\LearnBear\Injector;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use RuntimeException;

use function explode;
use function htmlspecialchars_decode;

class WorkflowTest extends TestCase
{
    protected ResourceInterface $resource;

    protected function setUp(): void
    {
        $stubSession = $this->createStub(SessionHandlerInterface::class);
        $stubSession->method('isNotAuthorized')->willReturn(false);
        $injector = Injector::getOverrideInstance('html-app', new class ($stubSession) extends AbstractModule{
            public function __construct(
                private readonly SessionHandlerInterface $sessionHandlerStub
            ) {
            }

            protected function configure(): void
            {
                $this->bind(SessionHandlerInterface::class)->toInstance($this->sessionHandlerStub);
            }
        });
        $this->resource = $injector->getInstance(ResourceInterface::class);
    }

    /**
     * @return array{path: string, queryStr?: string}
     * @psalm-return non-empty-list<string>
     */
    protected function getLinkUrlFromAtag(string $roStr, string $linkId): array
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
        $inputPassword = 'Fuga.1234';

        // 実行
        $loginRo = $this->resource->post($requestPath, ['username' => $inputUsername, 'password' => $inputPassword]);

        // 検証
        $this->assertSame(Code::SEE_OTHER, $loginRo->code);

        $html = $loginRo->toString();
        [$path, $queryStr] = $this->getLinkUrlFromAtag($html, 'redirect-to');

        return $path . '?' . $queryStr;
    }

    /**
     * @depends testLoginAllow
     */
    public function testNext(string $requestPath): string
    {
        // 実行
        $nextRo = $this->resource->get($requestPath);

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
        $ro = $this->resource->get($requestPath);

        // 検証
        $this->assertSame(Code::OK, $ro->code);
        $html = $ro->toString();
        $this->assertNotEmpty($html);

        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $element = $dom->getElementById('starting-point');
        $this->assertNotNull($element);
        $this->assertSame('h1', $element->tagName);
        $this->assertSame('index', $element->textContent);

        return $ro;
    }
}
