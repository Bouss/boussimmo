<?php

namespace App\Tests\Util;

use App\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testContainsReturnsTrueWhenStringContainsOneTheWords(): void
    {
        $words = ['word1', 'word2', 'word3'];
        $string = 'Foo : word1, bar.';

        $this->assertTrue(StringUtil::contains($string, $words));
    }

    public function testContainsReturnsFalseWhenStringDoesNotContainOneTheWords(): void
    {
        $words = ['word1', 'word2', 'word3'];
        $string = 'Foo : bar, qux.';

        $this->assertFalse(StringUtil::contains($string, $words));
    }
}
