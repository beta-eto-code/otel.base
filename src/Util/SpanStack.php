<?php

namespace Otel\Base\Util;

use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use SplStack;

class SpanStack
{
    private array $list = [];
    private SplStack $stack;

    public function __construct()
    {
        $this->stack = new SplStack();
    }

    public function add(SpanInterface|ReadableSpanInterface $span): bool
    {
        if (!$this->isExist($span->getName())) {
            $this->list[] = $span->getName();
            $this->stack->push($span);
            return true;
        }

        return false;
    }

    public function remove($spanName): bool
    {
        if ($this->isExist($spanName) && $this->isCurrent($spanName)) {
            unset($this->list[$spanName]);
            $this->stack->pop();
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
        $span = $this->stack->top();
        return ($span && $span->getName() === $spanName);
    }

    public function getByName($spanName): SpanInterface|ReadableSpanInterface|null
    {
        foreach ($this->stack as $item) {
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

    public function removeCurrent(): SpanInterface|ReadableSpanInterface|null
    {
        $span = $this->stack->pop();
        unset($this->list[$span->getName()]);
        return $span;
    }

    public function getCurrent(): SpanInterface|ReadableSpanInterface|null
    {
        if ($this->stack->isEmpty()) {
            return null;
        }
        return $this->stack->top();
    }
}
