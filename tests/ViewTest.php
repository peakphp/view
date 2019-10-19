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

    public function testHasAndSetHelper()
    {
        $view = new View();
        $view->setHelper('test', new ViewHelperA());
        $this->assertTrue($view->hasHelper('test'));
        $this->assertFalse($view->hasHelper('test2'));
    }

    public function testRenderWithChildren()
    {
        $view = new View(
            [
                'name' => 'foo'
            ],
            new Presentation(['/layout2.php' => ['/profile2.php']], FIXTURES_PATH.'/scripts')
        );
        $content = $view->render();
        $this->assertTrue($content === '<div class="content"><h1>Profile of foo</h1><h1>child1 of foo</h1><h1>child2 of foo and bob</h1><h1>orphan1 of foobar</h1></div>');
    }

    public function testRenderWithDirectives1()
    {
        $view = new View([
            'name' => 'foo',
            'profile' => [
                'email' => 'bar@bar.foo'
            ]
        ], new Presentation(['/directives1.php'], FIXTURES_PATH.'/scripts'));

        $view->setDirectives([
            new \Peak\View\Directive\EchoDirective()
        ]);

        $content = $view->render();
        $this->assertTrue($content === '<h1>Directives</h1><h2>foo</h2><h3>bar@bar.foo</h3>');
    }

    public function testRenderWithDirectives2()
    {
        $view = new View([
            'name' => 'foo',
            'profile' => [
                'email' => 'bar@bar.foo'
            ]
        ], new Presentation(['/directives2.php'], FIXTURES_PATH.'/scripts'));

        $view
            ->setHelpers([
                'testHelper' => new ViewHelperA(),
            ])
            ->setDirectives([
                new \Peak\View\Directive\EchoDirective(),
                new \Peak\View\Directive\FnDirective()
            ]);

        $content = $view->render();
        echo $content;
        $this->assertTrue($content === '<h1>Directives</h1><h2>foo</h2><h3>1</h3><h4>'.date('Y').'</h4><h5>Hello you!</h5>');
    }
}
