<?php

namespace Otel\Base\Abstracts;


use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\Util\SpanStack;


abstract class OTeBaselSpanManager implements OTelSpanManagerInterface
{
    private TracerProviderInterface $tracerProvider;
    private ?TracerInterface $tracer;

    /**
     * @var SpanStack<SpanInterface>
     */
    private SpanStack $spanStack;
    private ?iterable $eventListener;

    /**
     * @param TracerProviderInterface $tracerProvider
     * @param iterable|null $eventIterator
     */
    public function __construct(TracerProviderInterface $tracerProvider, ?iterable $eventIterator = null)
    {
        $this->tracerProvider = $tracerProvider;
        $this->setTracer(null);
        $this->eventListener = $eventIterator;
        $this->spanStack = new SpanStack();
    }


    public function __destruct()
    {
        foreach ($this->spanStack as $span) {
            $span->end();
        }

        $this->tracerProvider->shutdown();
    }

    public function startSpan($name, $attributes = []): void
    {
        if ($this->spanStack->isExist($name)) {
            $span = $this->spanStack->getByName($name);

            foreach ($attributes as $key => $value) {
                $span->setAttribute($key, $value);
            }

            return;
        }

        $span = $this->getTracer()
            ->spanBuilder($name)
            ->setSpanKind(SpanKind::KIND_SERVER)
            ->startSpan();

        foreach ($attributes as $key => $value) {
            $span->setAttribute($key, $value);
        }

        $this->spanStack->add($span);

        $span->activate();

    }

    public function addSpanEvent(string $spanName, string $eventName, ?array $attributes): void
    {
        if ($this->spanStack->isExist($spanName)) {
            $this->getSpan($spanName)->addEvent($eventName, $attributes);
        }
    }

    public function addSpanAttributes(string $spanName, ?array $attributes): void
    {
        if ($this->spanStack->isExist($spanName)) {
            $span = $this->getSpan($spanName);
            foreach ($attributes as $key => $value) {
                $span->setAttribute($key, $value);
            }
        }
    }

    public function endSpan(string $spanName): void
    {
        if ($spanName == $this->getRootSpanName()) {
            throw new \RuntimeException('Root span cannot be ended');
        }

        if (!$this->spanStack->isExist($spanName)) {
            throw new \RuntimeException('Span ' . $spanName . ' not found');
        }

        if ($this->spanStack->isCurrent($spanName)) {
            $this->getSpan($spanName)->end();

            $this->spanStack->remove($spanName);
        } else {
            throw new \RuntimeException('Span ' . $spanName . ' has opened sub spans');
        }
    }

    public function getTracer(): TracerInterface
    {
        if (is_null($this->tracer)) {
            $this->setTracer(null);
        }
        return $this->tracer;
    }

    public function setTracer(?string $tracerName): void
    {
        if ($tracerName == null) {
            $tracerName = getenv('LO_OTEL_TRACER_NAME') || self::OTEL_TRACER_NAME_DEFAULT;
        }
        $this->tracer = $this->tracerProvider->getTracer($tracerName);
    }

    public function getRootSpanName(): string
    {
        return self::ROOT_SPAN_NAME;
    }

    public function startRootSpan(?array $attributes = []): void
    {
        if ($this->spanStack->isExist($this->getRootSpanName())) {
            $this->addSpanAttributes(self::ROOT_SPAN_NAME, $attributes);
        } else {
            $this->startSpan($this->getRootSpanName(), $attributes);
        }
    }

    public function getSpan(string $spanName): ?SpanInterface
    {
        return $this->spanStack->getByName($spanName);
    }

    public function getEventListener(): ?iterable
    {
        return $this->eventListener;
    }

    public function getSpansNames(): array
    {
        return $this->spanStack->getNames();
    }
}
