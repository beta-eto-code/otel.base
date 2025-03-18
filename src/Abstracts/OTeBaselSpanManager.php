<?php

namespace Otel\Base\Abstracts;


use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;


abstract class OTeBaselSpanManager implements OTelSpanManagerInterface
{
    private TracerProviderInterface $tracerProvider;
    private ?TracerInterface $tracer;

    /**
     * @var array<SpanInterface>
     */
    private array $spanList = [];

    public function __construct(TracerProviderInterface $tracerProvider)
    {
        $this->tracerProvider = $tracerProvider;
        $this->setTracer(null);
    }


    public function __destruct()
    {
        foreach ($this->spanList as $span) {
            $span->end();
        }

        $this->tracerProvider->shutdown();
    }

    private function addSpan(string $name, SpanInterface $span): void
    {
        $this->spanList[$name] = $span;
    }

    public function startSpan($name, $attributes = []): void
    {
        if (array_key_exists($name, $this->spanList)) {
            throw new \RuntimeException('Span ' . $name . ' already started');
        }

        $span = $this->getTracer()
            ->spanBuilder($name)
            ->setSpanKind(SpanKind::KIND_SERVER)
            ->startSpan();

        foreach ($attributes as $key => $value) {
            $span->setAttribute($key, $value);
        }

        $this->addSpan($name, $span);

        $span->activate();

    }

    public function addSpanEvent(string $spanName, string $eventName, ?array $attributes): void
    {
        if (array_key_exists($spanName, $this->spanList)) {
            $this->getSpan($spanName)->addEvent($eventName, $attributes);
        }
    }

    public function addSpanAttributes(string $spanName, ?array $attributes): void
    {
        if (array_key_exists($spanName, $this->spanList)) {
            $span = $this->getSpan($spanName);
            foreach ($attributes as $key => $value) {
                $span->setAttribute($key, $value);
            }
        }
    }

    public function endSpan(string $spanName): void
    {
        if (!array_key_exists($spanName, $this->spanList)) {
            throw new \RuntimeException('Span ' . $spanName . ' not found');
        }

        if ($spanName == $this->getRootSpanName()) {
            throw new \RuntimeException('Root span cannot be ended');
        }

        $this->getSpan($spanName)->end();

        unset($this->spanList[$spanName]);
    }

    public function getTracer(): TracerInterface
    {
        if (is_null($this->tracer )) {
            $this->setTracer(null);
        }
        return $this->tracer;
    }

    public function setTracer(?string $tracerName): void
    {
        if ($tracerName == null) {
            $tracerName = getenv('OTEL_TRACER_NAME') || self::OTEL_TRACER_NAME_DEFAULT;
        }
        $this->tracer = $this->tracerProvider->getTracer($tracerName);
    }

    public function getRootSpanName(): string
    {
        return self::ROOT_SPAN_NAME;
    }

    public function startRootSpan(?array $attributes = [])
    {
        if (array_key_exists($this->getRootSpanName(), $this->spanList)) {
            $this->addSpanAttributes(self::ROOT_SPAN_NAME, $attributes);
        } else {
            $this->startSpan($this->getRootSpanName(), $attributes);
        }
    }

    public function getSpan(string $spanName): ?SpanInterface
    {
        return $this->spanList[$spanName];
    }
}
