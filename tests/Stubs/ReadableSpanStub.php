<?php

namespace Otel\Base\Tests\Stubs;

use OpenTelemetry\API\Trace as API;
use OpenTelemetry\SDK\Common\Instrumentation\InstrumentationScopeInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;
use OpenTelemetry\SDK\Trace\SpanDataInterface;

class ReadableSpanStub implements ReadableSpanInterface
{

    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getContext(): API\SpanContextInterface
    {
        // TODO: Implement getContext() method.
    }

    public function getParentContext(): API\SpanContextInterface
    {
        // TODO: Implement getParentContext() method.
    }

    public function getInstrumentationScope(): InstrumentationScopeInterface
    {
        // TODO: Implement getInstrumentationScope() method.
    }

    public function hasEnded(): bool
    {
        // TODO: Implement hasEnded() method.
    }

    public function toSpanData(): SpanDataInterface
    {
        // TODO: Implement toSpanData() method.
    }

    public function getDuration(): int
    {
        // TODO: Implement getDuration() method.
    }

    public function getKind(): int
    {
        // TODO: Implement getKind() method.
    }

    public function getAttribute(string $key)
    {
        // TODO: Implement getAttribute() method.
    }
}

;
