<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\ArgumentValueResolver;

use MccApiTools\RequestObjectBundle\Model\Context;
use MccApiTools\RequestObjectBundle\Utils\HttpRequestParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestContextValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (false === $this->supports($argument)) {
            return;
        }

        $object = new Context;

        $object->method = $request->getMethod();
        $object->keys = self::keysByRequest($request);

        yield $object;
    }

    private function supports(ArgumentMetadata $argument): bool
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
