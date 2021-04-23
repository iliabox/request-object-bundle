<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Tests\Model;

use MccApiTools\RequestObjectBundle\Model\Collection;
use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;

class RequestDto3 extends Collection implements RequestObjectInterface
{
    public static function getItemClass(): string
    {
        return RequestDto1::class;
    }
}
