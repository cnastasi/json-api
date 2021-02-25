<?php

declare(strict_types=1);

namespace CNastasi\JsonApi\Example;

use CNastasi\DDD\Contract\Comparable;
use CNastasi\DDD\Contract\Creatable;
use CNastasi\DDD\Error\IncomparableObjects;
use CNastasi\DDD\ValueObject\AbstractEntity;
use CNastasi\DDD\ValueObject\CreatableTrait;
use CNastasi\DDD\ValueObject\IntegerIdentifier;
use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\DDD\ValueObject\Primitive\Text;

class User extends AbstractEntity implements Creatable
{
    use CreatableTrait;

    private Text $name;

    public function __construct(IntegerIdentifier $id, Text $name, DateTime $createdAt)
    {
        parent::__construct($id);

        $this->name = $name;
        $this->createdAt = $createdAt;
    }

    public function getName(): Text
    {
        return $this->name;
    }

    public function equalsTo(Comparable $item): bool
    {
        if ($item instanceof User) {
            return $this->getId()->equalsTo($item->getId());
        }

        throw new IncomparableObjects($item, $this);
    }
}