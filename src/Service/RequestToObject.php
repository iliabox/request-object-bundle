<?php

namespace MccApiTools\RequestObjectBundle\Service;

use MccApiTools\RequestObjectBundle\Model\RequestableInterface;
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
        $data = self::dataByRequest($request);

        try {
            /* @var $object RequestableInterface */
            $object = $this->serializer->denormalize($data, $class, null, ['allow_extra_attributes' => false]);
        } catch (ExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $object;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \JsonException
     */
    private static function dataByRequest(Request $request): array
    {
        switch ($request->getMethod()) {
            case 'GET':
                $query = [];
                foreach ($request->query->getIterator() as $key => $value) {
                    $query[str_replace('-', '_', $key)] = $value;
                }

                return $query;
            case 'POST':
            case 'PATCH':
            case 'PUT':
                return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            default:
                throw new MethodNotAllowedHttpException(['GET', 'POST', 'PATCH', 'PUT'], sprintf("Method %s not supported.", $request->getMethod()));
        }
    }

    public static function keysByRequest(Request $request): array
    {
        $data = self::dataByRequest($request);

        return array_keys($data);
    }
}
