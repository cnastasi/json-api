<?php

declare(strict_types=1);

namespace CNastasi\JsonApi\Example;

use CNastasi\DDD\Collection\EntityCollection;

/**
 * Class UserCollection
 * @package CNastasi\JsonApi\Example
 *
 * @implements EntityCollection<User>
 */
class UserCollection extends EntityCollection
{
    public function getItemType(): string
    {
        return User::class;
    }
}