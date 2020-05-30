<?php

namespace MccApiTools\RequestObjectBundle\ArgumentValueResolver;

use MccApiTools\RequestObjectBundle\Model\Context;
use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestContextValueResolver implements ArgumentValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $object = new Context;

        $object->method = $request->getMethod();
        $object->keys = RequestToObject::keysByRequest($request);

        yield $object;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Context::class;
    }
}
