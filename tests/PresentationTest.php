<?php

use \PHPUnit\Framework\TestCase;

use Peak\View\Presentation\Presentation;
use Peak\View\Presentation\SingleLayoutPresentation;
use Peak\View\Presentation\SinglePresentation;

class PresentationTest extends TestCase
{
    public function testGetSources()
    {
        $scripts = [
            '/layout.php' => [
                '/script.php',
                '/layout2,php' => [
                    '/script2.php',
                    '/script3.php',
                ]
            ]
        ];
        $presentation = new Presentation($scripts);

        $this->assertTrue(is_array($presentation->getSources()));
        $this->assertTrue($presentation->getSources() === $scripts);
    }

    public function testSetPath()
    {
        $scripts = [
            '/layout.php' => [
                '/script.php',
                '/layout2,php' => [
                    '/script2.php',
                    '/script3.php',
                ]
            ]
        ];

        $scriptsWithPath = [
            __DIR__.'/layout.php' => [
                __DIR__.'/script.php',
                __DIR__.'/layout2,php' => [
                    __DIR__.'/script2.php',
                    __DIR__.'/script3.php',
                ]
            ]
        ];

        $presentation = new Presentation($scripts, __DIR__);
        $this->assertTrue($presentation->getSources() === $scriptsWithPath);
    }

    public function testSingleLayoutPresentation()
    {
        $presentation = new SingleLayoutPresentation('layout', 'script');
        $this->assertTrue($presentation->getSources() === ['layout' => ['script']]);
    }

    public function testSinglePresentation()
    {
        $presentation = new SinglePresentation('script');
        $this->assertTrue($presentation->getSources() === ['script']);
    }
}
