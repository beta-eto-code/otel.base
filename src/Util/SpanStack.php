<?php

namespace Otel\Base\Util;

use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use SplStack;

class SpanStack
{
    private array $list = [];
    private SplStack $queue;

    public function __construct()
    {
        $this->queue = new SplStack();
    }

    public function add(SpanInterface|ReadableSpanInterface $span): bool
    {
        if (!$this->isExist($span->getName())) {
            $this->list[] = $span->getName();
            $this->queue->push($span);
            return true;
        }

        return false;
    }

    public function remove($spanName): bool
    {
        if ($this->isExist($spanName) && $this->isCurrent($spanName)) {
            unset($this->list[$spanName]);
            $this->queue->pop();
            return true;
        }
        return false;
    }

    public function isExist($spanName): bool
    {
        return in_array($spanName, $this->list);
    }

    public function isCurrent($spanName): bool
    {
        $span = $this->queue->top();
        return ($span && $span->getName() === $spanName);
    }

    public function getByName($spanName): SpanInterface|ReadableSpanInterface|null
    {
        foreach ($this->queue as $item) {
            if ($item->getName() == $spanName) {
                return $item;
            }
        }

        return null;
    }

    public function getNames(): array
    {
        return $this->list;
    }
}
