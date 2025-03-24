<?php

namespace Otel\Base\Interfaces;

interface EventInterface
{
    public function getName(): string;
    public function getSpanName(): string;
    public function getAttributes(): array;
}
