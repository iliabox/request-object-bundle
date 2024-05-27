<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\ArgumentValueResolver;

use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use MccApiTools\RequestObjectBundle\Service\RequestValidator;
use MccApiTools\RequestObjectBundle\Model\RequestableInterface;

class RequestObjectValueResolver implements ValueResolverInterface
{
    public function __construct(readonly private RequestToObject  $requestDenormalizer,
                                readonly private RequestValidator $requestValidator)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (false === $this->supports($argument)) {
            return;
        }

        $classname = $argument->getType();

        $object = $this->requestDenormalizer->createObject($request, $classname);

        $errors = $this->requestValidator->errors($object);
        if (count($errors) > 0) {
            throw new BadRequestHttpException(json_encode($errors));
        }

        yield $object;
    }

    private function supports(ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), RequestableInterface::class);
    }
}
