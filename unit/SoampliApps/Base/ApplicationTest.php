<?php
namespace SoampliApps\Base;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application($this->getMock('\SoampliApps\Base\Containers\Container'), __DIR__.'/test');
    }

    public function testCanDetectCliExecutionContext()
    {
        $this->assertEquals('cli', $this->application->getExecutionContext());
    }

    public function testCanGetContainerFromApplication()
    {
        $this->assertInstanceOf('\SoampliApps\Base\Containers\Container', $this->application->getContainer());
    }

    public function testCanGetApplicationRootFolder()
    {
        $this->assertEquals(__DIR__.'/test', $this->application->getApplicationRootFolder());
        $this->application = new Application($this->getMock('\SoampliApps\Base\Containers\Container'));
        $this->assertEquals(str_replace('unit/', '', __DIR__.'/'), $this->application->getApplicationRootFolder());
    }

    public function testCanOverrideConfigurationKey()
    {
        //
    }

    /**
     * @covers SoampliApps\Base\Application::boot
     */
    public function testBootSequence()
    {
        //
    }

    public function testCanRegisterInvokableFunctions()
    {
        $this->application->registerInvokableFunction('testFunction', function() {
            return 'test';
        });
        $this->assertEquals('test', $this->application->testFunction());
        $this->setExpectedException('\RuntimeException');
        $this->application->fakeFunction();
    }

    /**
     * @covers SoampliApps\Base\Application::registerServiceProvider
     */
    public function testCanRegisterServiceProviders()
    {
        $service_provider = $this->getMock('\SoampliApps\Base\ServiceProviders\ServiceProviderInterface');
        $service_provider->expects($this->once())->method('register');
        $service_provider->expects($this->once())->method('getBootPriority')->will($this->returnValue(1));
        $this->application->registerServiceProvider($service_provider);
    }
}
