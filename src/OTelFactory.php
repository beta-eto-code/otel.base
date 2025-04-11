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
    private string $fileName;
    private ?iterable $eventIterator;

    public function __construct(string $fileName = 'php://stdout', ?iterable $eventIterator = null)
    {
        $this->fileName = $fileName;
        $this->eventIterator = $eventIterator;
    }

    /**
     * @throws Exception
     */
    public function create(ServerRequestInterface $request): OTelSpanManagerInterface
    {
        $stream = fopen($this->fileName, 'w');
        if ($stream === false) {
            throw new Exception("Unable to open file '$this->fileName'.");
        }

        $tracerProvider = new TracerProvider(
            new SimpleSpanProcessor(
                new ConsoleSpanExporter(
                    new StreamTransport($stream, 'json')
                )
            )
        );

        $spanManager = new OTelSpanManager($tracerProvider, $this->eventIterator);
        $spanManager->startRootSpan([
            'http.method' => $request->getMethod(),
            'http.url' => $request->getUri()->getPath(),
        ]);
        return $spanManager;
    }
}
