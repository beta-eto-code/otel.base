<?php

namespace Otel\Base;

use OpenTelemetry\SDK\Common\Export\Stream\StreamTransport;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Otel\Base\Interfaces\OTelFactoryInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Psr\Http\Message\ServerRequestInterface;

class OTelFactory implements OTelFactoryInterface
{
    public function createDefault(): OTelSpanManagerInterface
    {
        $stream = fopen('php://output', 'w');

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
        $spanManager = $this->createDefault();

        $spanManager->startRootSpan([
            'http.method' => $request->getMethod(),
            'http.url' => $request->getUri()->getPath(),
        ]);
    }

}
