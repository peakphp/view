<?php

use \PHPUnit\Framework\TestCase;
use \Peak\Backpack\Bedrock\AppBuilder;
use \Peak\Blueprint\Bedrock\Application as ApplicationBlueprint;
use \Peak\Bedrock\Application\Application;
use \Peak\Bedrock\Kernel;
use \Peak\Bedrock\Http\Request\HandlerResolver;
use \Peak\Di\Container;
use \Psr\Container\ContainerInterface;

class AppBuilderTest extends TestCase
{
    public function testDefault()
    {
        $app = (new AppBuilder())
            ->build();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertInstanceOf(ApplicationBlueprint::class, $app);
        $this->assertInstanceOf(ContainerInterface::class, $app->getContainer());
        $this->assertInstanceOf(Container::class, $app->getContainer());
        $this->assertNull($app->getProps());
    }

    public function testSetEnv()
    {
        $app = (new AppBuilder())
            ->setEnv('staging')
            ->build();

        $this->assertTrue($app->getKernel()->getEnv() === 'staging');
    }

    public function testSetHandlerResolver()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject */
        $handlerResolver = $this->createMock(HandlerResolver::class);
        $handlerResolver->test = 'foobar';

        $app = (new AppBuilder())
            ->setHandlerResolver($handlerResolver)
            ->build();

        $this->assertTrue($app->getHandlerResolver()->test === 'foobar');
    }

    public function testSetContainer()
    {
        $container = new \Peak\Di\Container();
        $container->set(new \Peak\Collection\Collection(['foo' => 'bar']));
        $app = (new AppBuilder())
            ->setContainer($container)
            ->build();

        $appContainer = $app->getContainer();
        $this->assertInstanceOf(\Peak\Collection\Collection::class, $appContainer->get(\Peak\Collection\Collection::class));
        $this->assertTrue($appContainer->get(\Peak\Collection\Collection::class)['foo'] === 'bar');
    }

    public function testSetAppName()
    {
        $app = (new AppBuilder())
            ->setClassName(\Peak\Backpack\Bedrock\Application::class)
            ->build();

        $this->assertInstanceOf(\Peak\Backpack\Bedrock\Application::class, $app);
    }

    public function testSetKernel()
    {
        $kernel = $this->createMock(Kernel::class);
        $kernel->expects(($this->once()))
            ->method('getEnv')
            ->will($this->returnValue('foobar'));

        $app = (new AppBuilder())
            ->setKernel($kernel)
            ->build();

        $this->assertTrue($app->getKernel()->getEnv() === 'foobar');
    }

    public function testSetProps()
    {
        $app = (new AppBuilder())
            ->setProps(new \Peak\Collection\PropertiesBag(['test' => 'foobar']))
            ->build();

        $this->assertTrue($app->hasProp('test'));
    }

    public function testExecuteAfterBuild()
    {
        $app = (new AppBuilder())
            ->executeAfterBuild(function($app) {
                $app->test = 'foobar';
            })
            ->build();

        $this->assertTrue($app->test === 'foobar');
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Error
     */
    public function testTriggerKernelError1()
    {
        $kernel = $this->createMock(Kernel::class);

        $app = (new AppBuilder())
            ->setEnv('barfoo')
            ->setKernel($kernel)
            ->build();
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Error
     */
    public function testTriggerKernelError2()
    {
        $kernel = $this->createMock(Kernel::class);
        $container = $this->createMock(Container::class);
        $app = (new AppBuilder())
            ->setContainer($container)
            ->setKernel($kernel)
            ->build();
    }
}
