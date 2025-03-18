<?php

namespace Otel\Base\Interfaces;

use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\SDK\Trace\ReadableSpanInterface;

interface OTelSpanManagerInterface
{
    const string ROOT_SPAN_NAME = 'root';
    const string OTEL_TRACER_NAME_DEFAULT = 'io.opentelemetry.contrib.php';
    public function startSpan(string $name, ?array $attributes = []): void;

    public function endSpan(string $spanName): void;

    public function addSpanEvent(string $spanName, string $eventName, ?array $attributes): void;

    public function addSpanAttributes(string $spanName, array $attributes): void;

    public function getRootSpanName(): string;

    public function getSpan(string $spanName): ?SpanInterface;
}
