<?php

use PHPUnit\Framework\TestCase;

use Peak\Common\ClassFinder;

class ClassFinderTest extends TestCase
{
	/**
     * test findFirst()
     */
    function testFindFirst()
    {
        // both namespace contains a Json class
        $cf = new ClassFinder([
            'Peak\Bedrock\View\Render',
            'Peak\Config\File'
        ]);

        $class = $cf->findFirst('Json');
        $this->assertTrue($class === Peak\Bedrock\View\Render\Json::class);

        $class = null;
        $class = $cf->findFirst('Test');
        $this->assertNull($class);
    }

    /**
     * test findLast()
     */
    function testFindLast()
    {
        // both namespace contains a Json class
        $cf = new ClassFinder([
            'Peak\Bedrock\View\Render',
            'Peak\Config\File'
        ]);

        $class = $cf->findLast('Json');
        $this->assertTrue($class === Peak\Config\File\Json::class);

        $class = null;
        $class = $cf->findFirst('Test');
        $this->assertNull($class);
    }

    /**
     * Test suffix() and prefix()
     */
    function testSuffixPrefix()
    {
        $cf = new ClassFinder([
            'Peak\Bedrock\Application\Bootstrap'
        ]);

        $cf->setPrefix('Config');
        $cf->setSuffix('');

        $class = $cf->findLast('CustomRoutes');
        $this->assertTrue($class === Peak\Bedrock\Application\Bootstrap\ConfigCustomRoutes::class);
    }
        	  
}