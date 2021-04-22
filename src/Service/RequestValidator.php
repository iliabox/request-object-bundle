<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Service;

use MccApiTools\RequestObjectBundle\Model\Collection;
use MccApiTools\RequestObjectBundle\Model\RequestableInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidator
{

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function errors(RequestableInterface $dto): array
    {
        $object = is_subclass_of($dto, Collection::class) ? $dto->items : $dto;

        $violations = $this->validator->validate($object);

        return self::createErrorsByViolations($violations);
    }

    /**
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[] $violations
     * @return array
     */
    private static function createErrorsByViolations(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $prop = trim($violation->getPropertyPath(), '[]');
            if (!isset($errors[$prop])) {
                $errors[$prop] = [];
            }
            $errors[$prop][] = $violation->getMessage();
        }

        return $errors;
    }
}
