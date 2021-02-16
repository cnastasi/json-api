<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use Psr\Http\Message\ResponseInterface;

interface HttpResponseFactory
{
    public function make(string $content, array $headers, int $statusCode = 200): ResponseInterface;

    public function json(array $body, array $headers = [], int $statusCode = 200, string $contentType = 'application/json'): ResponseInterface;
}