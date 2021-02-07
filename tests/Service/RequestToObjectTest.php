<?php

namespace MccApiTools\RequestObjectBundle\Tests\Service;

use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use MccApiTools\RequestObjectBundle\Tests\Model\RequestDto1;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RequestToObjectTest extends TestCase
{
    private ?RequestToObject $service;

    protected function setUp(): void
    {
        parent::setUp();

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $serializer = new Serializer($normalizers, $encoders);

        $this->service = new RequestToObject($serializer);
    }

    /**
     * @dataProvider goodContentProvider
     * @covers
     */
    public function testCreateObject(array $content)
    {
        $request = new Request([], [], [], [], [], [], json_encode($content));

        $dto = $this->service->createObject($request, RequestDto1::class);

        self::assertInstanceOf(RequestDto1::class, $dto);
        self::assertSame($content['id'], $dto->id);
        self::assertSame($content['name'], $dto->name);
    }

    /**
     * @dataProvider badContentProvider
     * @covers
     */
    public function testFailedCreateObject(array $content)
    {
        $request = new Request([], [], [], [], [], [], json_encode($content));

        $this->expectExceptionMessageMatches('/Extra attributes are not allowed/');
        $this->expectException(BadRequestHttpException::class);
        $this->service->createObject($request, RequestDto1::class);
    }

    public function goodContentProvider(): \Generator
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
    }

    public function badContentProvider(): \Generator
    {
        yield [
            ['id' => 10, 'name' => 'test name', 'extra' => 'extra field'],
        ];
    }
}
