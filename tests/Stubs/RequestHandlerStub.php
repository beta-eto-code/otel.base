<?php

namespace Otel\Base\Tests\Stubs;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerStub implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new class implements ResponseInterface {

            public function getProtocolVersion(): string
            {
                // TODO: Implement getProtocolVersion() method.
            }

            public function withProtocolVersion(string $version): MessageInterface
            {
                // TODO: Implement withProtocolVersion() method.
            }

            public function getHeaders(): array
            {
                // TODO: Implement getHeaders() method.
            }

            public function hasHeader(string $name): bool
            {
                // TODO: Implement hasHeader() method.
            }

            public function getHeader(string $name): array
            {
                // TODO: Implement getHeader() method.
            }

            public function getHeaderLine(string $name): string
            {
                // TODO: Implement getHeaderLine() method.
            }

            public function withHeader(string $name, $value): MessageInterface
            {
                // TODO: Implement withHeader() method.
            }

            public function withAddedHeader(string $name, $value): MessageInterface
            {
                // TODO: Implement withAddedHeader() method.
            }

            public function withoutHeader(string $name): MessageInterface
            {
                // TODO: Implement withoutHeader() method.
            }

            public function getBody(): StreamInterface
            {
                // TODO: Implement getBody() method.
            }

            public function withBody(StreamInterface $body): MessageInterface
            {
                // TODO: Implement withBody() method.
            }

            public function getStatusCode(): int
            {
                // TODO: Implement getStatusCode() method.
            }

            public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
            {
                // TODO: Implement withStatus() method.
            }

            public function getReasonPhrase(): string
            {
                // TODO: Implement getReasonPhrase() method.
            }
        };
    }

}
