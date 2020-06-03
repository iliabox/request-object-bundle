<?php

namespace MccApiTools\RequestObjectBundle\Service;

use MccApiTools\RequestObjectBundle\Model\QueryObjectInterface;
use MccApiTools\RequestObjectBundle\Model\RequestableInterface;
use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;
use MccApiTools\RequestObjectBundle\Utils\HttpRequestParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RequestToObject
{

    private DenormalizerInterface $serializer;

    public function __construct(DenormalizerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @param string $class
     * @return RequestableInterface
     * @throws \JsonException
     */
    public function createObject(Request $request, string $class): RequestableInterface
    {
        try {
            $data = self::dataByRequest($request, $class);

            return $this->serializer->denormalize($data, $class, null, ['allow_extra_attributes' => false]);
        } catch (ExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param string $class
     * @return array
     * @throws \JsonException
     */
    private static function dataByRequest(Request $request, string $class): array
    {
        if (is_subclass_of($class, QueryObjectInterface::class)) {
            return HttpRequestParser::dataQuery($request);
        }

        if (is_subclass_of($class, RequestObjectInterface::class)) {
            return HttpRequestParser::dataRequest($request);
        }

        if (is_subclass_of($class, RequestableInterface::class)) {
            return $request->getMethod() === Request::METHOD_GET
                ? HttpRequestParser::dataQuery($request)
                : HttpRequestParser::dataRequest($request);
        }

        throw new \InvalidArgumentException(sprintf('Unknown class "%s".', $class));
    }
}
