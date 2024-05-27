<?php

namespace MccApiTools\RequestObjectBundle\Tests\Service;

use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use MccApiTools\RequestObjectBundle\Tests\Model\RequestDto2;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RequestToObjectExtraFieldsTest extends TestCase
{
    private ?RequestToObject $service;

    protected function setUp(): void
    {
        parent::setUp();

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $serializer = new Serializer($normalizers, $encoders);

        $this->service = new RequestToObject($serializer);
    }

    #[DataProvider('contentProvider')]
    public function testCreateObject(array $content): void
    {
        $request = new Request([], [], [], [], [], [], json_encode($content));

        $dto = $this->service->createObject($request, RequestDto2::class);

        self::assertInstanceOf(RequestDto2::class, $dto);
        self::assertSame($content['id'], $dto->id);
        self::assertSame($content['name'], $dto->name);
    }

    public static function contentProvider(): \Generator
    {
        yield [
            ['id' => 10, 'name' => 'test name'],
        ];

        yield [
            ['id' => -100, 'name' => ''],
        ];

        yield [
            ['id' => 0, 'name' => '0'],
        ];

        yield [
            ['id' => 10, 'name' => 'test name', 'extra' => 'extra field'],
        ];
    }
}
