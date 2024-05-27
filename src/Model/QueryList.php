<?php

declare(strict_types=1);

namespace MccApiTools\RequestObjectBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class QueryList implements QueryObjectInterface
{
    public string $query = '';

    #[Assert\GreaterThan(0)]
    public string $page = '1';

    #[Assert\GreaterThan(0)]
    public string $per_page = '10';

    #[Assert\Regex('/^[a-z_\.]+\|(asc|desc)$/i')]
    public string $order_by = 'createdAt|desc';

    public function getSort(): string
    {
        return explode('|', $this->order_by)[0];
    }

    public function getOrder(): string
    {
        return explode('|', $this->order_by)[1];
    }
}
