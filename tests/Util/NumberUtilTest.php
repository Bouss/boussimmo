<?php

namespace App\Tests\Util;

use App\Util\NumberUtil;
use PHPUnit\Framework\TestCase;

class NumberUtilTest extends TestCase
{
    public function testExtractInt(): void
    {
        $val = 'foo1234 bar';

        $this->assertEquals(1234, NumberUtil::extractInt($val));
    }

    public function testExtractFloat(): void
    {
        $val = 'foo12,34 bar';

        $this->assertEquals(12.34, NumberUtil::extractFloat($val));
    }
}