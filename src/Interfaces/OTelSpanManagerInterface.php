<?php

namespace Otel\Base\Interfaces;

use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;

interface OTelSpanManagerInterface
{
    public const string ROOT_SPAN_NAME = 'root';
    public const string OTEL_TRACER_NAME_DEFAULT = 'io.opentelemetry.contrib.php';
    public function createAndStartSpan(string $name, ?array $attributes = []): void;

    public function endSpan(): void;

    public function addSpanEvent(string $spanName, string $eventName, ?array $attributes): void;

    public function getSpan(): ?SpanInterface;

    public function getEventListener(): ?iterable;

    public function startRootSpan(?array $attributes = []): void;
}
