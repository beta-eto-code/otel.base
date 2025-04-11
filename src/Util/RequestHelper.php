<?php

namespace Otel\Base\Util;

use Otel\Base\Interfaces\OTelSpanManagerInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestHelper
{
    public const SPAN_MANAGER_ATTRIBUTE = 'otel.request.span.manager';

    public static function getSpanManagerFromRequest(ServerRequestInterface $request): ?OTelSpanManagerInterface
    {
        $spanManager = $request->getAttribute(self::SPAN_MANAGER_ATTRIBUTE);
        return $spanManager instanceof OTelSpanManagerInterface ? $spanManager : null;
    }

    public static function provideSpanManagerToRequest(
        ServerRequestInterface $request,
        OTelSpanManagerInterface $spanManager
    ): ServerRequestInterface {
        return $request->withAttribute(
            self::SPAN_MANAGER_ATTRIBUTE,
            $spanManager
        );
    }
}
