<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Model;

abstract class Collection implements RequestableInterface
{
    public array $items = [];

    abstract public static function getItemClass(): string;

    public static function create(array $items): self
    {
        $me = new static();
        $me->items = $items;

        return $me;
    }
}
