<?php

namespace MccApiTools\RequestObjectBundle\Service;

use MccApiTools\RequestObjectBundle\Model\RequestableInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidator
{

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function errors(RequestableInterface $dto): array
    {
        $violations = $this->validator->validate($dto);

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
