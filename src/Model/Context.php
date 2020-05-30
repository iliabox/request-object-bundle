<?php

namespace MccApiTools\RequestObjectBundle\Model;

class Context
{
    /**
     * @var string
     */
    public string $method;

    /**
     * @var array
     */
    public array $keys = [];
}
