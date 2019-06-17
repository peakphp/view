<?php

use \PHPUnit\Framework\TestCase;

use Peak\View\View;
use Peak\View\Presentation\Presentation;
use Peak\View\Presentation\PresentationInterface;

require_once FIXTURES_PATH.'/helpers/ViewHelperA.php';

class ViewTest extends TestCase
{
    public function testBasic()
    {
        $view = new View(null, $this->createMock(PresentationInterface::class));
        $this->assertTrue(is_array($view->getVars()));
        $this->assertTrue(empty($view->getVars()));
        $this->assertTrue($view->getPresentation() instanceof PresentationInterface);
    }

    public function testBasic2()
    {
        $view = new View();
        $this->assertTrue($view->getPresentation() === null);
    }

    public function testGetVars()
    {
        $view = new View(['test' => 'foobar'], $this->createMock(PresentationInterface::class));
        $vars = $view->getVars();
        $this->assertTrue(isset($vars['test']));
        $this->assertTrue($vars['test'] === 'foobar');
        $this->assertFalse(isset($vars['test2']));
    }

    public function testVarGetter()
    {
        $view = new View(['test' => 'foobar'], $this->createMock(PresentationInterface::class));
        $this->assertTrue($view->test === 'foobar');
    }

    public function testVarGetterException()
    {
        $this->expectException(\Exception::class);
        $view = new View([], $this->createMock(PresentationInterface::class));
        $view->test;
    }

    public function testVarIsset()
    {
        $view = new View(['test' => 'foobar'], $this->createMock(PresentationInterface::class));
        $this->assertTrue(isset($view->test));
        $this->assertFalse(isset($view->test2));
    }

    public function testAddMacro()
    {
        $view = new View(['name' => 'foobar'], $this->createMock(PresentationInterface::class));
        $view->setMacro('macro1', function() {
            return $this->name;
        });
        $this->assertTrue($view->hasMacro('macro1'));
        $this->assertFalse($view->hasMacro('macro2'));
        $this->assertTrue($view->macro1() === 'foobar');
    }

    public function testMacroHelperException()
    {
        $this->expectException(\RuntimeException::class);
        $view = new View([], $this->createMock(PresentationInterface::class));
        $view->macro();
    }

    public function testHelper()
    {
        $view = new View([], $this->createMock(PresentationInterface::class));
        $view->setHelpers([
            'myHelper' => new ViewHelperA(),
        ]);

        $this->assertTrue($view->myHelper('bob') === 'Hello bob!');
    }

    public function testRender()
    {
        $view = new View(
            ['name' => 'foo'],
            new Presentation(['/layout.php' => ['/profile.php']], FIXTURES_PATH.'/scripts')
        );
        $content = $view->render();
        $this->assertTrue($content === '<div class="content"><h1>Profile of foo</h1></div>');
    }

    public function testRenderSingleViewScript()
    {
        $view = new View(
            ['name' => 'foo'],
            new Presentation(['/profile.php'], FIXTURES_PATH.'/scripts')
        );
        $content = $view->render();
        $this->assertTrue($content === '<h1>Profile of foo</h1>');

        $view = new View(
            ['name' => 'foo'],
            new Presentation([FIXTURES_PATH.'/scripts/profile.php'])
        );
        $content = $view->render();
        $this->assertTrue($content === '<h1>Profile of foo</h1>');
    }

    public function testRenderFail()
    {
        $this->expectException(\Peak\View\Exception\RenderException::class);
        $view = new View([],
            new Presentation(['/layout.php' => ['/unknown.php']], FIXTURES_PATH . '/scripts')
        );
        $view->render();
    }

    public function testRenderFail2()
    {
        $this->expectException(\Peak\View\Exception\RenderException::class);
        $view = new View();
        $view->render();
    }
}
