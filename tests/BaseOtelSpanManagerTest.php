<?php

namespace Otel\Base\Tests;

use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use Otel\Base\OTelMiddleware;
use Otel\Base\Tests\Stubs\OTelFactoryStub;
use Otel\Base\Tests\Stubs\RequestHandlerStub;
use Otel\Base\Tests\Stubs\RequestStub;
use PHPUnit\Framework\TestCase;

class BaseOtelSpanManagerTest extends TestCase
{
    public function testSpan()
    {
        $otelManager = (new OTelFactoryStub())->createDefault();
        $otelManager->createAndStartSpan('test');

        $this->assertNotNull($otelManager->getSpan());

        $otelManager->endSpan();

        $this->assertNull($otelManager->getSpan());
    }

    public function testRootSpanOpenClose()
    {
        $otelManager = (new OTelFactoryStub())->createDefault();
        $otelManager->startRootSpan([
            'http.method' => 'POST',
            'http.url' => '/test'
        ]);
        $this->assertNotNull($otelManager->getSpan());
        $otelManager->endSpan();
    }

    public function testStartSpan()
    {
        $factory = new OTelFactoryStub();
        $m = new OTelMiddleware($factory);
        $m->process(new RequestStub(), new RequestHandlerStub() );

        $rootSpan = $factory->getSpanManager()->getSpan('root');
        $this->assertNotNull($rootSpan);
    }

    public function testSpanAddEvent()
    {
        $factory = new OTelFactoryStub();
        $m = new OTelMiddleware($factory);
        $m->process(new RequestStub(), new RequestHandlerStub() );

        $spanManager = $factory->getSpanManager();
        $spanManager->addSpanEvent('root', 'eventName', [
            'm0' => 'v0',
        ]);

        /** @var ReadableSpanInterface $span */
        $span = $spanManager->getSpan();
        $t =  $span->toSpanData()->getEvents();

        $this->assertTrue(count($t) === 1);
    }
}
