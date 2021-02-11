<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\ValueObjectSerializer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;


final class SimpleApiSerializer
{
    private ValueObjectSerializer $serializer;
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ValueObjectSerializer $serializer, ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function serialize($object, PaginationInfo $paginationInfo = null, int $statusCode = 200): ResponseInterface
    {
        $body = [];

        $body['data'] = $this->serializeData($object);

        if ($paginationInfo) {
            $body['pagination'] = $this->serializeResource($paginationInfo);
        }

        return $this->buildResponse($body, $statusCode);
    }

    public function accept($object): bool
    {
        return true;
    }

    private function buildResponse(array $data, int $statusCode): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($statusCode);

        $content = \json_encode($data, JSON_THROW_ON_ERROR);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($content));
    }

    private function serializeResource($object): array
    {
        return $this->serializer->serialize($object, true);
    }

    private function serializeCollection(Collection $collection): array
    {
        $data = [];

        foreach ($collection as $object) {
            $data [] = $this->serializeResource($object);
        }

        return $data;
    }

    private function serializeData($object): array {
        return $object instanceof Collection
            ? $this->serializeCollection($object)
            : $this->serializeResource($object);
    }
}