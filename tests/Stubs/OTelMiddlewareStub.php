<?php

namespace Otel\Base\Tests\Stubs;

use Otel\Base\OTelMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OTelMiddlewareStub extends OTelMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return parent::process($request, $handler); // TODO: Change the autogenerated stub
    }

}
