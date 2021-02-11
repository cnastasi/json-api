<?php

declare(strict_types=1);

namespace CNastasi\JsonApi\Example;

use CNastasi\DDD\Contract\Identifier;
use CNastasi\DDD\ValueObject\AbstractEntity;
use CNastasi\DDD\ValueObject\IntegerIdentifier;
use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\DDD\ValueObject\Primitive\Text;

class User extends AbstractEntity
{
    private Text $name;

    public function __construct(IntegerIdentifier $id, Text $name, DateTime $createdAt)
    {
        parent::__construct($id, $createdAt);

        $this->name = $name;
    }

    public function getName(): Text
    {
        return $this->name;
    }
}