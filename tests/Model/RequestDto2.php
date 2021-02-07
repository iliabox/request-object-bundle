<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Tests\Model;

use MccApiTools\RequestObjectBundle\Model\AllowExtraAttributesInterface;
use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;

class RequestDto2 implements RequestObjectInterface, AllowExtraAttributesInterface
{
    public int $id;

    public string $name;
}
