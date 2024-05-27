<?php

namespace MccApiTools\RequestObjectBundle\Utils;

use Symfony\Component\HttpFoundation\Request;

class HttpRequestParser
{
    public static function dataQuery(Request $request): array
    {
        $query = [];
        foreach ($request->query->getIterator() as $key => $value) {
            $query[str_replace('-', '_', $key)] = $value;
        }

        return $query;
    }

    /**
     * @throws \JsonException
     */
    public static function dataRequest(Request $request): array
    {
        if ($jsonContent = $request->getContent()) {
            return json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        }

        return $request->request->all();
    }
}
