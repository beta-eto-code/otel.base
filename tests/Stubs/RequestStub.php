<?php

namespace Otel\Base\Tests\Stubs;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class RequestStub implements ServerRequestInterface
{


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

    public function getRequestTarget(): string
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function withMethod(string $method): RequestInterface
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri(): UriInterface
    {
        return new class implements UriInterface {

            public function getScheme(): string
            {
                // TODO: Implement getScheme() method.
            }

            public function getAuthority(): string
            {
                // TODO: Implement getAuthority() method.
            }

            public function getUserInfo(): string
            {
                // TODO: Implement getUserInfo() method.
            }

            public function getHost(): string
            {
                // TODO: Implement getHost() method.
            }

            public function getPort(): ?int
            {
                // TODO: Implement getPort() method.
            }

            public function getPath(): string
            {
               return '/';
            }

            public function getQuery(): string
            {
                // TODO: Implement getQuery() method.
            }

            public function getFragment(): string
            {
                // TODO: Implement getFragment() method.
            }

            public function withScheme(string $scheme): UriInterface
            {
                // TODO: Implement withScheme() method.
            }

            public function withUserInfo(string $user, ?string $password = null): UriInterface
            {
                // TODO: Implement withUserInfo() method.
            }

            public function withHost(string $host): UriInterface
            {
                // TODO: Implement withHost() method.
            }

            public function withPort(?int $port): UriInterface
            {
                // TODO: Implement withPort() method.
            }

            public function withPath(string $path): UriInterface
            {
                // TODO: Implement withPath() method.
            }

            public function withQuery(string $query): UriInterface
            {
                // TODO: Implement withQuery() method.
            }

            public function withFragment(string $fragment): UriInterface
            {
                // TODO: Implement withFragment() method.
            }

            public function __toString(): string
            {
                // TODO: Implement __toString() method.
            }
        };
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        // TODO: Implement withUri() method.
    }

    public function getServerParams(): array
    {
        // TODO: Implement getServerParams() method.
    }

    public function getCookieParams(): array
    {
        // TODO: Implement getCookieParams() method.
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        // TODO: Implement withCookieParams() method.
    }

    public function getQueryParams(): array
    {
        // TODO: Implement getQueryParams() method.
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        // TODO: Implement withQueryParams() method.
    }

    public function getUploadedFiles(): array
    {
        // TODO: Implement getUploadedFiles() method.
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        // TODO: Implement withUploadedFiles() method.
    }

    public function getParsedBody()
    {
        // TODO: Implement getParsedBody() method.
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        // TODO: Implement withParsedBody() method.
    }

    public function getAttributes(): array
    {
        // TODO: Implement getAttributes() method.
    }

    public function getAttribute(string $name, $default = null)
    {
        // TODO: Implement getAttribute() method.
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        // TODO: Implement withAttribute() method.
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        // TODO: Implement withoutAttribute() method.
    }
}
