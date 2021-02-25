<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\ValueObject\Primitive\Date;
use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\Example\Address;
use CNastasi\Example\Age;
use CNastasi\Example\Name;
use CNastasi\Example\Person;
use CNastasi\Serializer\Converter\CollectionConverter;
use CNastasi\Serializer\Converter\CompositeValueObjectConverter;
use CNastasi\Serializer\Converter\DateTimeConverter;
use CNastasi\Serializer\Converter\SimpleValueObjectConverter;
use CNastasi\Serializer\DefaultSerializer;
use CNastasi\Serializer\SerializationLoopGuard;
use CNastasi\Serializer\SerializerOptionsDefault;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SerializationTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function shouldSerializeSimpleValueObject(): void
    {
        $name = 'Uncle Scrooge';
        $age = 99;
        $street = 'Via dei Glicini, 43';
        $city = 'Paperopoli';
        $birthDate = '2000-10-24 00:00:00';
        $flag = true;

        $expectedData = [
            'data' => [
                'type' => 'Person',
                'attributes' => [
                    'name' => $name,
                    'age' => $age,
                    'address' => [
                        'street' => $street,
                        'city' => $city
                    ],
                    'phone' => null,
                    'flag' => $flag,
                    'birthDate' => $birthDate
                ]
            ]
        ];


        $serializer = new DefaultSerializer(
            [
                new DateTimeConverter(),
                new SimpleValueObjectConverter(),
                new CompositeValueObjectConverter(),
                new CollectionConverter(),
            ],
            [
            ],
            new SerializationLoopGuard(),
            new SerializerOptionsDefault(false)
        );

        $classMapper = $this->prophesize(JsonApiClassMapper::class);
        $classMapper->getType(Person::class)->shouldBeCalledOnce()->willReturn('Person');

        $apiSerializer = new JsonApiSerializer($serializer, $classMapper->reveal());

        $valueObject = new Person(new Name($name), new Age($age), new Address($street, $city), DateTime::fromString($birthDate), $flag);

        $data = $apiSerializer->serialize($valueObject);

        self::assertSame($expectedData, $data);
    }
}