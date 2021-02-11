<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

interface JsonApiClassMapper
{
    public function getType (string $className): string;

    public function getClass (string $type): string;
}