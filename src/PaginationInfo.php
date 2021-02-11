<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\CompositeValueObject;

class PaginationInfo implements CompositeValueObject
{
    protected int $count;
    private int $page;
    private int $limit;

    public function __construct(int $count, int $page, int $limit)
    {
        $this->count = $count;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}