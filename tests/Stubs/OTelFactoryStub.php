<?php

namespace Otel\Base\Tests\Stubs;

use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\OTelFactory;
use Psr\Http\Message\ServerRequestInterface;

class OTelFactoryStub extends OTelFactory
{
    private ?OTelSpanManagerInterface $spanManager = null;

    public function create(ServerRequestInterface $request): OTelSpanManagerInterface
    {
        return $this->spanManager = parent::create($request);
    }

    public function getSpanManager(): ?OTelSpanManagerInterface
    {
        return $this->spanManager;
    }
}
