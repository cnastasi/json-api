<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\Collection;
use CNastasi\Serializer\Contract\ValueObjectSerializer;

final class SimpleApiSerializer implements ApiSerializer
{
    private ValueObjectSerializer $serializer;

    public function __construct(ValueObjectSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize($object, PaginationInfo $paginationInfo = null): array
    {
        $body = [];

        $body['data'] = $this->serializeData($object);

        if ($paginationInfo) {
            $body['pagination'] = $this->serializeResource($paginationInfo);
        }

        return $body;
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