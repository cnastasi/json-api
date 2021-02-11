<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\ValueObjectSerializer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;


final class JsonApiSerializer
{
    private ValueObjectSerializer $serializer;
    private ResponseFactoryInterface $responseFactory;
    private JsonApiClassMapper $classMapper;
    private StreamFactoryInterface $streamFactory;

    public function __construct(ValueObjectSerializer $serializer, ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory, JsonApiClassMapper $classMapper)
    {
        $this->serializer = $serializer;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->classMapper = $classMapper;
    }

    public function serialize($object, int $statusCode = 200): ResponseInterface
    {
        $data = $object instanceof Collection
            ? $this->serializeCollection($object)
            : $this->serializeResource($object);

        $body = [
            'data' => $data
        ];

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
            ->withHeader('Content-Type', 'application/vnd.api+json')
            ->withBody($this->streamFactory->createStream($content));
    }

    private function serializeResource($object): array
    {
        $data = $this->serializer->serialize($object, true);

        return [
            'type' => $this->classMapper->getType(get_class($object)),
            'attributes' => $data,
        ];
    }

    private function serializeCollection(Collection $collection): array
    {
        $data = [];

        foreach ($collection as $object) {
            $data [] = $this->serializeResource($object);
        }

        return $data;
    }
}