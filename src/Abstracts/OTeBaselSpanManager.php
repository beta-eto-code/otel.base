<?php

namespace Otel\Base\Abstracts;


use Exception;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\Util\SpanStack;


abstract class OTeBaselSpanManager implements OTelSpanManagerInterface
{
    protected TracerProviderInterface $tracerProvider;
    protected ?TracerInterface $tracer;

    /**
     * @var SpanStack<SpanInterface>
     */
    protected SpanStack $spanStack;

    abstract protected function onDestruct(): void;

    /**
     * @param TracerProviderInterface $tracerProvider
     */
    public function __construct(TracerProviderInterface $tracerProvider)
    {
        $this->tracerProvider = $tracerProvider;
        $this->setTracer(null);
        $this->spanStack = new SpanStack();
    }


    public function __destruct()
    {
        $this->onDestruct();
        $this->tracerProvider->shutdown();
    }

    /**
     * @throws Exception
     */
    public function createAndStartSpan($name, $attributes = []): void
    {
        if ($this->spanStack->isExist($name) && ($name == $this->getRootSpanName())) {
            return;
        }

        if ($this->spanStack->isExist($name)) {
            throw new Exception('Span already exists');
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
            $this->getSpan()->addEvent($eventName, $attributes);
        }
    }

    public function endSpan(): void
    {
        $span = $this->spanStack->removeCurrent();
        if (!is_null($span)) {
            $span->end();
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

    private function getRootSpanName(): string
    {
        return self::ROOT_SPAN_NAME;
    }

    /**
     * @throws Exception
     */
    public function startRootSpan(?array $attributes = []): void
    {
        if (!$this->spanStack->isExist($this->getRootSpanName())) {
            $this->createAndStartSpan($this->getRootSpanName(), $attributes);
        }
    }

    public function getSpan(): ?SpanInterface
    {
        return $this->spanStack->getCurrent();
    }

    public function getSpansNames(): array
    {
        return $this->spanStack->getNames();
    }
}
