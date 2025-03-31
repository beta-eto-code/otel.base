<?php

namespace Otel\Base;

use Exception;
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
        $stream = fopen('php://stdout', 'w');

        $tracerProvider = new TracerProvider(
            new SimpleSpanProcessor(
                new ConsoleSpanExporter(
                    new StreamTransport($stream, 'json')
                )
            )
        );

        return new OTelSpanManager($tracerProvider);
    }

    /**
     * @throws Exception
     */
    public function create(ServerRequestInterface $request): void
    {
        if (!OTelRegistry::has('default')) {
            OTelRegistry::register('default', $this->createDefault());
        }

        $spanManager = OTelRegistry::get('default');

        $spanManager->startRootSpan([
            'http.method' => $request->getMethod(),
            'http.url' => $request->getUri()->getPath(),
        ]);
    }

}
