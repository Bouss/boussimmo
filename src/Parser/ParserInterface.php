<?php

namespace App\Parser;

use App\DTO\Property;
use App\Exception\ParseException;

interface ParserInterface
{
    /**
     * @param string $html
     * @param array  $filters
     * @param array  $params
     *
     * @return Property[]
     *
     * @throws ParseException
     */
    public function parse(string $html, array $filters = [], array $params = []): array;
}
