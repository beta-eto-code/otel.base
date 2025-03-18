<?php

namespace Otel\Base\Tests\Stubs;

use OpenTelemetry\SDK\Common\Export\Stream\StreamTransport;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\OTelFactory;
use Otel\Base\OTelSpanManager;
use Psr\Http\Message\ServerRequestInterface;

class OTelFactoryStub extends OTelFactory
{
    private OTelSpanManagerInterface $spanManager;

    public function createDefault(): OTelSpanManagerInterface
    {
        $stream = fopen('php://input', 'w');

        $tracerProvider = new TracerProvider(
            new SimpleSpanProcessor(
                new ConsoleSpanExporter(
                    new StreamTransport($stream, 'json')
                )
            )
        );

        return new OTelSpanManager($tracerProvider);
    }

    public function create(ServerRequestInterface $request): void
    {
        $this->spanManager = $this->createDefault();

        $this->spanManager->startRootSpan([
            'http.method' => $request->getMethod(),
            'http.url' => $request->getUri()->getPath(),
        ]);
    }

    /**
     * @return OTelSpanManagerInterface
     */
    public function getSpanManager() : OTelSpanManagerInterface
    {
        return $this->spanManager;
    }


}
