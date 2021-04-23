<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Tests\Model;

use MccApiTools\RequestObjectBundle\Model\AllowExtraAttributesInterface;
use MccApiTools\RequestObjectBundle\Model\Collection;
use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;

class RequestDto4 extends Collection implements RequestObjectInterface, AllowExtraAttributesInterface
{
    public static function getItemClass(): string
    {
        return RequestDto1::class;
    }
}
