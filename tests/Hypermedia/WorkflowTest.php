<?php

declare(strict_types=1);

namespace Cw\LearnBear\Hypermedia;

use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use Cw\LearnBear\Injector;
use PHPUnit\Framework\TestCase;
use Ray\Di\InjectorInterface;

use function json_decode;

class WorkflowTest extends TestCase
{
    protected ResourceInterface $resource;
    protected InjectorInterface $injector;

    protected function setUp(): void
    {
        $this->injector = Injector::getInstance('app');
        $this->resource = $this->injector->getInstance(ResourceInterface::class);
    }

    public function testIndex(): ResourceObject
    {
        $index = $this->resource->get('/');
        $this->assertSame(200, $index->code);

        return $index;
    }

    /**
     * @depends testIndex
     */
    public function testNext(ResourceObject $index): ResourceObject
    {
        $json = (string) $index;
        $href = json_decode($json)->_links->next->href;
        $ro = $this->resource->get($href);
        $this->assertSame(200, $ro->code);

        return $ro;
    }
}
