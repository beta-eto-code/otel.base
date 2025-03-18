<?php

namespace Otel\Base;


use Otel\Base\Interfaces\OTelFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OTelMiddleware implements MiddlewareInterface
{
    private OTelFactoryInterface $otelSpanFactory;

    public function __construct(OTelFactoryInterface $otelSpanFactory)
    {
        $this->otelSpanFactory = $otelSpanFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->otelSpanFactory->create($request);
        return $handler->handle($request);
    }
}
