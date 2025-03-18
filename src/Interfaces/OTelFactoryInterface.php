<?php

namespace Otel\Base\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface OTelFactoryInterface
{
    public function create(ServerRequestInterface $request): void;
}
