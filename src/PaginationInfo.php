<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\Contract\Comparable;
use CNastasi\DDD\Contract\CompositeValueObject;
use CNastasi\DDD\Error\IncomparableObjects;
use CNastasi\JsonApi\Example\User;

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

    public function equalsTo(Comparable $item): bool
    {
        if ($item instanceof static) {
            return $this->count === $item->count
                && $this->page === $item->page
                && $this->limit === $item->limit;
        }

        throw new IncomparableObjects($item, $this);
    }
}