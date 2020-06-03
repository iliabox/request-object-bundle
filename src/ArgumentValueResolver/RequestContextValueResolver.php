<?php

namespace MccApiTools\RequestObjectBundle\ArgumentValueResolver;

use MccApiTools\RequestObjectBundle\Model\Context;
use MccApiTools\RequestObjectBundle\Utils\HttpRequestParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestContextValueResolver implements ArgumentValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $object = new Context;

        $object->method = $request->getMethod();
        $object->keys = self::keysByRequest($request);

        yield $object;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_a($argument->getType(), Context::class, true);
    }

    private static function keysByRequest(Request $request): array
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            $data = HttpRequestParser::dataQuery($request);
        } else {
            $data = HttpRequestParser::dataRequest($request);
        }

        return array_keys($data);
    }
}
