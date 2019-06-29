<?php

use \PHPUnit\Framework\TestCase;

use Peak\View\Helper\Text\Truncate;

class TruncateTest extends TestCase
{
    public function testDefaultUsage()
    {
        $truncate = new Truncate();

        $text1 = 'This is a really really long text with words and letters that you are reading right now';

        $this->assertTrue($truncate($text1, 10) === 'This is...');
        $this->assertTrue($truncate($text1, 10, ' ...') === 'This ...');
        $this->assertTrue($truncate($text1, 15, '...',true) === 'This is a re...');
        $this->assertTrue($truncate($text1, 15, '...',false) === 'This is a...');
        $this->assertTrue($truncate($text1, 20, '...',false, true) === 'This is a...right now');
    }
}
