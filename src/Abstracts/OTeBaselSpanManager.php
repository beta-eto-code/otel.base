<?php

namespace Otel\Base\Abstracts;


use EmptyIterator;
use Exception;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Otel\Base\Interfaces\EventInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\Util\SpanStack;
use SebastianBergmann\FileIterator\Iterator;


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
        foreach ($this->spanStack->stack as $span) {
            /**
             * @var SpanInterface $span
             */
            foreach ($this->getEventIteratorForSpan($span) as $event) {
                $span->addEvent($event->getName(), $event->getAttributes());
            }

            $span->end();
        }

        $this->tracerProvider->shutdown();
    }

    /**
     * @param SpanInterface $span
     * @return iterable|EventInterface[]
     */
    private function getEventIteratorForSpan(SpanInterface $span): iterable
    {
        if (empty($this->eventListener)) {
            return [];
        }

        if ($this->eventListener instanceof Iterator) {
            $this->eventListener = iterator_to_array($this->eventListener);
        }

        foreach ($this->eventListener as $event) {
            if ($event instanceof EventInterface && $event->getSpanName() === $span->getName()) {
                yield $event;
            }
        }

        return new EmptyIterator();
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

    private function addSpanAttributes(string $spanName, ?array $attributes): void
    {
        if ($this->spanStack->isExist($spanName)) {
            $span = $this->getSpan();
            foreach ($attributes as $key => $value) {
                $span->setAttribute($key, $value);
            }
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

    public function getEventListener(): ?iterable
    {
        return $this->eventListener;
    }

    public function getSpansNames(): array
    {
        return $this->spanStack->getNames();
    }
}
