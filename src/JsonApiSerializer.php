<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\ValueObjectSerializer;


final class JsonApiSerializer implements ApiSerializer
{
    private ValueObjectSerializer $serializer;
    private JsonApiClassMapper $classMapper;

    public function __construct(ValueObjectSerializer $serializer, JsonApiClassMapper $classMapper)
    {
        $this->serializer = $serializer;
        $this->classMapper = $classMapper;
    }

    public function serialize($object,  PaginationInfo $paginationInfo = null): array
    {
        $data = $object instanceof Collection
            ? $this->serializeCollection($object)
            : $this->serializeResource($object);

        $body = [
            'data' => $data
        ];

        return $body;
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