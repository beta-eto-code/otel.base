<?php

namespace Otel\Base;


use Exception;
use Otel\Base\Interfaces\OTelFactoryInterface;
use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Otel\Base\Util\RequestHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OTelMiddleware implements MiddlewareInterface
{
    private ?OTelSpanManagerInterface $spanManager;
    private ?OTelFactoryInterface $spanFactory;

    public static function initWithSpanManager(OTelSpanManagerInterface $spanManager): OTelMiddleware
    {
        return new static($spanManager);
    }

    public static function initWithFactory(OTelFactoryInterface $factory): OTelMiddleware
    {
        return new static(null, $factory);
    }

    private function __construct(
        ?OTelSpanManagerInterface $spanManager = null,
        ?OTelFactoryInterface $spanFactory = null
    ) {
        $this->spanManager = $spanManager;
        $this->spanFactory = $spanFactory;
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $spanManager = $this->spanManager ?? $this->getOTelFactory()->create($request);
        return $handler->handle(RequestHelper::provideSpanManagerToRequest(
            $request,
            $spanManager
        ));
    }

    private function getOTelFactory(): OTelFactoryInterface
    {
        if (is_null($this->spanFactory)) {
            $this->spanFactory = new OTelFactory();
        }
        return $this->spanFactory;
    }
}
