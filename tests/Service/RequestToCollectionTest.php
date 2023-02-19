<?php

namespace MccApiTools\RequestObjectBundle\Tests\Service;

use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use MccApiTools\RequestObjectBundle\Tests\Model\RequestDto1;
use MccApiTools\RequestObjectBundle\Tests\Model\RequestDto3;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RequestToCollectionTest extends TestCase
{
    private ?RequestToObject $service;

    protected function setUp(): void
    {
        parent::setUp();

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory), new ArrayDenormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $this->service = new RequestToObject($serializer);
    }

    #[DataProvider('goodContentProvider')]
    public function testCreateObject(array $content): void
    {
        $request = new Request([], [], [], [], [], [], json_encode($content));

        $dto = $this->service->createObject($request, RequestDto3::class);

        self::assertInstanceOf(RequestDto3::class, $dto);
        self::assertInstanceOf(RequestDto1::class, $dto->items[0]);
        self::assertSame($content[0]['id'], $dto->items[0]->id);
        self::assertSame($content[0]['name'], $dto->items[0]->name);
    }

    #[DataProvider('badContentProvider')]
    public function testFailedCreateObject(array $content, string $pattern): void
    {
        $request = new Request([], [], [], [], [], [], json_encode($content));

//        $this->expectExceptionMessageMatches($pattern);
        $this->expectException(BadRequestHttpException::class);
        $this->service->createObject($request, RequestDto3::class);
    }

    public static function goodContentProvider(): \Generator
    {
        yield [
            [['id' => 10, 'name' => 'test name']],
        ];

        yield [
            [['id' => -100, 'name' => '']],
        ];

        yield [
            [['id' => 0, 'name' => '0']],
        ];
    }

    public static function badContentProvider(): \Generator
    {
        yield [
            ['id' => 10, 'name' => 'test name'],
            '/Invalid collection/'
        ];

        yield [
            [['id' => 10, 'name' => 'test name', 'extra' => 'extra field']],
            '/Extra attributes are not allowed/'
        ];
    }
}
