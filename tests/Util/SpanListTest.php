<?php

namespace Otel\Base\Tests\Util;

use Otel\Base\Tests\Stubs\ReadableSpanStub;
use Otel\Base\Util\SpanStack;
use PHPUnit\Framework\TestCase;

class SpanListTest extends TestCase
{

    public function testAdd()
    {
        $spanList = new SpanStack();
        $item = new ReadableSpanStub();
        $item->setName('test');
        $spanList->add($item);

        $item2 = new ReadableSpanStub();
        $item2->setName('test2');
        $spanList->add($item2);

        $this->assertTrue($spanList->isExist('test'));

        $this->assertTrue($spanList->isCurrent('test2'));

        $item3 = new ReadableSpanStub();
        $item3->setName('test');
        $result = $spanList->add($item3);
        $this->assertFalse($result);
        $span = $spanList->getByName('test');

        $this->assertNotNull($span);
    }

    public function testRemove()
    {
        $spanList = new SpanStack();
        $item = new ReadableSpanStub();
        $item->setName('test');

        $spanList->add($item);

        $item2 = new ReadableSpanStub();
        $item2->setName('test2');
        $spanList->add($item2);

        $removeResult1 = $spanList->remove('test');

        $this->assertFalse($removeResult1);

        $removeResult2 = $spanList->remove('test2');
        $this->assertTrue($removeResult2);

        $removeResult3 = $spanList->remove('test');
        $this->assertTrue($removeResult3);

    }
}
