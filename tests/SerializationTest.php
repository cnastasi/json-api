<?php

declare(strict_types=1);

namespace Cnastasi\JsonApi;

use CNastasi\Example\Address;
use CNastasi\Example\Age;
use CNastasi\Example\Name;
use CNastasi\Example\Person;
use CNastasi\Serializer\Converter\CollectionConverter;
use CNastasi\Serializer\Converter\CompositeValueObjectConverter;
use CNastasi\Serializer\Converter\DateTimeImmutableConverter;
use CNastasi\Serializer\Converter\SimpleValueObjectConverter;
use CNastasi\Serializer\DefaultSerializer;
use CNastasi\Serializer\SerializationLoopGuard;
use CNastasi\Serializer\SerializerOptionsDefault;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
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
        $birthDate = '2000-10-24T00:00:00+00:00';
        $flag = true;


        $serializer = new DefaultSerializer(
            [
                new SimpleValueObjectConverter(),
                new CompositeValueObjectConverter(),
                new CollectionConverter(),
                new DateTimeImmutableConverter(),
            ],
            [
            ],
            new SerializationLoopGuard(),
            new SerializerOptionsDefault(false)
        );

        $classMapper = $this->prophesize(JsonApiClassMapper::class);
        $classMapper->getType(Person::class)->shouldBeCalledOnce()->willReturn('Person');

        $apiSerializer = new JsonApiSerializer($serializer, new ResponseFactory(), new StreamFactory(), $classMapper->reveal());

        $valueObject = new Person(new Name($name), new Age($age), new Address($street, $city), new \DateTimeImmutable($birthDate), $flag);

        $response = $apiSerializer->serialize($valueObject);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['application/vnd.api+json'], $response->getHeader('Content-Type'));

        $body = $response->getBody();
        $body->rewind();

        $content = $body->getContents();

        $data = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'data' => [
                'type' => 'Person',
                'attributes' => [
                    'name' => $name,
                    'age' => $age,
                    'address' => [
                        'street' => $street,
                        'city' => $city
                    ],
                    'birthDate' => $birthDate,
                    'flag' => $flag,
                    'phone' => null
                ]
            ]
        ], $data);
    }
}