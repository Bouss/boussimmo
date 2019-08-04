<?php

namespace App\Tests\Util;

use App\Util\NumericUtil;
use PHPUnit\Framework\TestCase;

class NumericUtilTest extends TestCase
{
    public function testExtractInt(): void
    {
        $val = 'foo1234 bar';

        $this->assertEquals(1234, NumericUtil::extractInt($val));
    }

    public function testExtractFloat(): void
    {
        $val = 'foo12,34 bar';

        $this->assertEquals(12.34, NumericUtil::extractFloat($val));
    }
}
