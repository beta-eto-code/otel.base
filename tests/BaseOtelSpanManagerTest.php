<?php

namespace Otel\Base\Tests;

use Exception;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use Otel\Base\OTelFactory;
use Otel\Base\OTelMiddleware;
use Otel\Base\Tests\Stubs\OTelFactoryStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BaseOtelSpanManagerTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSpan()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $otelManager = (new OTelFactory())->create($request);
        $otelManager->createAndStartSpan('test');
        $this->assertNotNull($otelManager->getSpan());

        $otelManager->endSpan();
        $this->assertNotNull($otelManager->getSpan());

        $otelManager->endSpan();
        $this->assertNull($otelManager->getSpan());
    }

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRootSpanOpenClose()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $otelManager = (new OTelFactoryStub())->create($request);
        $otelManager->startRootSpan([
            'http.method' => 'POST',
            'http.url' => '/test'
        ]);
        $this->assertNotNull($otelManager->getSpan());
        $otelManager->endSpan();
    }

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartSpan()
    {
        $factory = new OTelFactoryStub();
        $m = OTelMiddleware::initWithFactory($factory);
        $request = $this->createMock(ServerRequestInterface::class);
        $m->process($request, $this->createMock(RequestHandlerInterface::class));

        $rootSpan = $factory->getSpanManager()->getSpan();
        $this->assertNotNull($rootSpan);
    }

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSpanAddEvent()
    {
        $factory = new OTelFactoryStub();
        $m = OTelMiddleware::initWithFactory($factory);
        $request = $this->createMock(ServerRequestInterface::class);
        $m->process($request, $this->createMock(RequestHandlerInterface::class));

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
