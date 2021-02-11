<?php

declare(strict_types=1);

namespace CNastasi\JsonApi;

use CNastasi\DDD\ValueObject\IntegerIdentifier;
use CNastasi\DDD\ValueObject\Primitive\DateTime;
use CNastasi\DDD\ValueObject\Primitive\Text;
use CNastasi\Example\Address;
use CNastasi\Example\Age;
use CNastasi\Example\Name;
use CNastasi\Example\Person;
use CNastasi\JsonApi\Example\User;
use CNastasi\JsonApi\Example\UserCollection;
use CNastasi\Serializer\Converter\CollectionConverter;
use CNastasi\Serializer\Converter\CompositeValueObjectConverter;
use CNastasi\Serializer\Converter\DateTimeConverter;
use CNastasi\Serializer\Converter\DateTimeImmutableConverter;
use CNastasi\Serializer\Converter\SimpleValueObjectConverter;
use CNastasi\Serializer\DefaultSerializer;
use CNastasi\Serializer\SerializationLoopGuard;
use CNastasi\Serializer\SerializerOptionsDefault;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;

class SimpleApiSerializerTest extends TestCase
{
    use ProphecyTrait;

    private SimpleApiSerializer $apiSerializer;

    public function setUp(): void
    {
        parent::setUp();

        $serializer = new DefaultSerializer(
            [
                new DateTimeConverter(),
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

        $this->apiSerializer = new SimpleApiSerializer($serializer, new ResponseFactory(), new StreamFactory());
    }

    public function test_resorceSerialization(): void
    {
        $name = 'Uncle Scrooge';
        $age = 99;
        $street = 'Via dei Glicini, 43';
        $city = 'Paperopoli';
        $birthDate = '2000-10-24T00:00:00+00:00';
        $flag = true;

        $valueObject = new Person(
            new Name($name),
            new Age($age),
            new Address($street, $city),
            new \DateTimeImmutable($birthDate),
            $flag
        );

        $expectedResponse = [
            'data' => [
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
        ];

        $response = $this->apiSerializer->serialize($valueObject);

        $this->assertResponse($response, 200, $expectedResponse);
    }

    public function test_collectionSerialization(): void
    {
        //new PaginationInfo(100, 1, 15)

        $now = new \DateTimeImmutable();
        $nowAsString = $now->format('Y-m-d H:i:s');

        $expectedResponse = [
            'data' => [
                ['id' => 1, 'name' => 'Foo', 'createdAt' => $nowAsString],
                ['id' => 2, 'name' => 'Bar', 'createdAt' => $nowAsString],
                ['id' => 3, 'name' => 'Baz', 'createdAt' => $nowAsString],
            ],
            'pagination' => [
                'count' => 10,
                'page' => 2,
                'limit' => 3
            ]
        ];

        $collection = new UserCollection();

        $collection->addItem($this->makeUser(1, 'Foo', $now));
        $collection->addItem($this->makeUser(2, 'Bar', $now));
        $collection->addItem($this->makeUser(3, 'Baz', $now));

        $response = $this->apiSerializer->serialize($collection, new PaginationInfo(10, 2, 3));

        $this->assertResponse($response, 200, $expectedResponse);
    }

    private function assertResponse(ResponseInterface $response, int $statusCode, array $expectedResponse): void
    {
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['application/json'], $response->getHeader('Content-Type'));

        $body = $response->getBody();
        $body->rewind();

        $content = $body->getContents();

        $data = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        print_r($data);

        self::assertEquals($expectedResponse, $data);
    }

    private function makeUser(int $id, string $name, \DateTimeImmutable $now): User
    {
        return new User(new IntegerIdentifier($id), new Text($name), DateTime::fromDateTimeInterface($now));
    }
}