<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

interface HttpResponseFactory
{
    public function make(string $content, array $headers, int $statusCode = 200): ResponseInterface;

    public function json(array $body, array $headers = [], int $statusCode = 200, string $contentType = 'application/json'): ResponseInterface;

    public function stream(StreamInterface $stream, array $headers = [], int $statusCode = 200): ResponseInterface;
}