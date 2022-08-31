<?php

declare(strict_types=1);

namespace Cw\LearnBear\Resource\App;

use BEAR\Resource\ResourceInterface;
use Cw\LearnBear\Injector;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private ResourceInterface $resource;

    protected function setUp(): void
    {
        $injector = Injector::getInstance('app');
        $this->resource = $injector->getInstance(ResourceInterface::class);
    }

    public function testOnGet(): void
    {
        $ro = $this->resource->get('app://self/index');
        $this->assertSame(200, $ro->code);
        $this->assertSame('world', $ro->body['hello']);
    }
}
