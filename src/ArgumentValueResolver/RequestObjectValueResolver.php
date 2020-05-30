<?php

namespace MccApiTools\RequestObjectBundle\ArgumentValueResolver;

use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use MccApiTools\RequestObjectBundle\Service\RequestToObject;
use MccApiTools\RequestObjectBundle\Service\RequestValidator;
use MccApiTools\RequestObjectBundle\Model\RequestableInterface;

class RequestObjectValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RequestToObject
     */
    private RequestToObject $requestDenormalizer;

    /**
     * @var RequestValidator
     */
    private RequestValidator $requestValidator;

    public function __construct(RequestToObject $requestDenormalizer, RequestValidator $requestValidator)
    {
        $this->requestDenormalizer = $requestDenormalizer;
        $this->requestValidator = $requestValidator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $classname = $argument->getType();

        $object = $this->requestDenormalizer->createObject($request, $classname);

        $errors = $this->requestValidator->errors($object);
        if (count($errors) > 0) {
            throw new BadRequestHttpException(json_encode($errors));
        }

        yield $object;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), RequestableInterface::class);
    }
}
