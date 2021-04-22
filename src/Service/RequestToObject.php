<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Service;

use MccApiTools\RequestObjectBundle\Model\AllowExtraAttributesInterface;
use MccApiTools\RequestObjectBundle\Model\Collection;
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
            $isExtra = $this->isAllowExtraAttributes($class);

            $className = is_subclass_of($class, Collection::class) ? $class::getItemClass().'[]' : $class;

            $raw = $this->serializer->denormalize($data, $className, null, ['allow_extra_attributes' => $isExtra]);
        } catch (ExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (false === is_subclass_of($class, Collection::class)) {
            return $raw;
        }

        if (false === is_array($raw) && null !== $raw) {
            throw new BadRequestHttpException('Invalid data');
        }

        if (empty($raw)) {
            return $class::create([]);
        }

        if (empty($raw[0]) || false === ($raw[0] instanceof RequestableInterface)) {
            throw new BadRequestHttpException('Invalid collection');
        }

        return $class::create($raw);
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

    private function isAllowExtraAttributes(string $class): bool
    {
        switch (true) {
            case is_subclass_of($class, AllowExtraAttributesInterface::class):
                return true;
            default:
                return false;
        }
    }
}
