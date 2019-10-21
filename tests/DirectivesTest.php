<?php

use \PHPUnit\Framework\TestCase;

use Peak\View\View;
use Peak\View\Presentation\Presentation;

require_once FIXTURES_PATH.'/helpers/ViewHelperA.php';

class DirectivesTest extends TestCase
{
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
//        echo $content;
        $this->assertTrue($content === '<h1>Directives</h1><h2>foo</h2><h3>1</h3><h4>'.date('Y').'</h4><h5>Hello you!</h5>');
    }

    public function testRenderWithDirectives3()
    {
        $view = new View([
            'name' => 'foo',
            'profile' => [
                'email' => 'bar@bar.foo'
            ]
        ], new Presentation(['/directives3.php'], FIXTURES_PATH.'/scripts'));

        $view
            ->setMacro('test', function(...$args) {
                return 'ok';
            })
            ->setDirectives([
                new \Peak\View\Directive\EchoDirective(),
                new \Peak\View\Directive\FnDirective()
            ]);

        $content = $view->render();
//        echo $content;
        $this->assertTrue($content === '<h1>Hello</h1>ok');

    }
}
