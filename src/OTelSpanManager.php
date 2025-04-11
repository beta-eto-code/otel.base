<?php

namespace Otel\Base;

use EmptyIterator;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\SDK\Trace\Span;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Otel\Base\Abstracts\OTeBaselSpanManager;
use Otel\Base\Interfaces\EventInterface;
use SebastianBergmann\FileIterator\Iterator;

class OTelSpanManager extends OTeBaselSpanManager
{
    private ?iterable $eventListener;

    public function __construct(TracerProviderInterface $tracerProvider, ?iterable $eventIterator = null)
    {
        parent::__construct($tracerProvider);
        $this->eventListener = $eventIterator;
    }

    public function getEventListener(): ?iterable
    {
        return $this->eventListener;
    }

    protected function onDestruct(): void
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
    }

    /**
     * @param SpanInterface $span
     * @return iterable|EventInterface[]
     */
    private function getEventIteratorForSpan(SpanInterface $span): iterable
    {
        if (!$span instanceof Span) {
            return [];
        }

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
}
