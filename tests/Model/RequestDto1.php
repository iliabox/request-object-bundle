<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Tests\Model;

use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;

class RequestDto1 implements RequestObjectInterface
{
    public int $id;

    public string $name;
}
