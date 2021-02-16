<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpResponseFactoryImpl implements HttpResponseFactory
{
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function make(string $content, array $headers, int $statusCode = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($statusCode);

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response->withBody($this->streamFactory->createStream($content));
    }

    public function json(array $body,array $headers = [], int $statusCode = 200, string $contentType = 'application/json'): ResponseInterface
    {
        $headers = array_merge($headers, ['Content-Type' => $contentType]);

        $content = \json_encode($body, JSON_THROW_ON_ERROR);

        return $this->make($content, $headers, $statusCode);
    }
}