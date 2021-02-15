<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

interface ApiSerializer
{
    public function serialize($object, PaginationInfo $paginationInfo = null): array;
}