<?php

namespace App\Parser;

use App\DTO\PropertyAd;
use App\Exception\ParseException;

interface ParserInterface
{
    /**
     * @param string $html
     * @param array  $filters
     * @param array  $params
     *
     * @return PropertyAd[]
     *
     * @throws ParseException
     */
    public function parse(string $html, array $filters = [], array $params = []): array;
}
