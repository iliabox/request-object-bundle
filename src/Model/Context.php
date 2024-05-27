<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Model;

class Context
{
    public string $method;

    public array $keys = [];
}
