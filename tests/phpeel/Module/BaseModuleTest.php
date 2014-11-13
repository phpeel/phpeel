<?php
namespace Phpeel\System\Tests\Module;

use Phpeel\System\Enums\ModuleFilter;
use Phpeel\System\Enums\ResponseCode;
use Phpeel\System\Request\Request;
use Phpeel\System\Response\Response;
use Phpeel\System\Variable\Client;
use Phpeel\System\Variable\Server;

class BaseModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultInitialize()
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $this->assertInstanceOf('Phpeel\System\Module\ModuleData', $stub->getModuleData());
        $this->assertNull($stub->getModuleData()->getModuleName());
        $this->assertNull($stub->getModuleData()->getSceneName());
        $this->assertNull($stub->getRequest());
        $this->assertNotNull($stub->getResponse());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $stub->getResponse());
        $this->assertCount(0, $stub->getFilters(ModuleFilter::INPUT_EXECUTE));
    }
    
    public function testOptionalInitialize()
    {
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
        Server::capture();
        Client::capture();
        
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpeel\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        
        $this->assertInstanceOf('Phpeel\System\Module\ModuleData', $stub->getModuleData());
        $this->assertSame('top', $stub->getModuleData()->getModuleName());
        $this->assertSame('index', $stub->getModuleData()->getSceneName());
        $this->assertNotNull($stub->getRequest());
        $this->assertInstanceOf('Phpeel\System\Request\Request', $stub->getRequest());
        $this->assertNotNull($stub->getResponse());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $stub->getResponse());
        $this->assertCount(0, $stub->getFilters(ModuleFilter::INPUT_EXECUTE));
    }
    
    public function testSetRequestSuccess()
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $this->assertNull($stub->getRequest());
        $this->assertNotNull($stub->getResponse());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $stub->getResponse());
        $stub->setRequest(Request::getInstance(true));
        $this->assertNotNull($stub->getRequest());
        $this->assertInstanceOf('Phpeel\System\Request\Request', $stub->getRequest());
    }
    
    public function testSetResponseSuccess()
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $this->assertNull($stub->getRequest());
        $this->assertNotNull($stub->getResponse());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $stub->getResponse());
        $stub->setResponse(new Response(ResponseCode::INTERNAL_SERVER_ERROR));
        $this->assertNotNull($stub->getResponse());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $stub->getResponse());
        $this->assertSame(ResponseCode::INTERNAL_SERVER_ERROR, $stub->getResponse()->getResponseCode()->getValue());
    }
    
    public function providerControlFilterSuccess()
    {
        return [
            [ ModuleFilter::INPUT_EXECUTE, [ 'test1' ] ]
        ];
    }

    /**
     * @dataProvider providerControlFilterSuccess
     */
    public function testControlFilterSuccess($type, $list)
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $this->assertCount(0, $stub->getFilters($type));
        $method = (new \ReflectionClass('Phpeel\System\Module\BaseModule'))->getMethod('setFilters');
        $method->setAccessible(true);
        $method->invokeArgs($stub, [ $type, $list ]);
        $this->assertCount(1, $stub->getFilters($type));
    }
    
    public function providerPropertiesFailed()
    {
        return [
            [ null, 'InvalidArgumentException' ],
            [ '', 'InvalidArgumentException' ],
            [ true, 'InvalidArgumentException' ],
            [ false, 'InvalidArgumentException' ],
            [ [], 'InvalidArgumentException' ],
            [ [ 'a' ], 'InvalidArgumentException' ],
            [ new \stdClass(), 'InvalidArgumentException' ],
            [ 0, 'InvalidArgumentException' ],
            [ 0.0, 'InvalidArgumentException' ],
            [ 0.1, 'InvalidArgumentException' ],
            [ '0', 'InvalidArgumentException' ],
            [ '0.0', 'InvalidArgumentException' ],
            [ '0.1', 'InvalidArgumentException' ],
        ];
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testGetFilterFailed($type, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
            
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        $stub->getFilters($type);
    }
    
    public function providerDestructCallback()
    {
        return [
            [
                function () {
                    // nothing
                }
            ],
            [
                [
                    function () {
                        // nothing
                    }
                ],
                [
                    function () {
                        // nothing
                    }
                ],
            ]
        ];
    }

    /**
     * @dataProvider providerDestructCallback
     */
    public function testDestructCallback($callback)
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $method = (new \ReflectionClass('Phpeel\System\Module\BaseModule'))->getMethod('entryDestructCallback');
        $method->setAccessible(true);
        $method->invokeArgs($stub, [ $callback ]);
    }
    
    public function testOverloadMethods()
    {
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass('Phpeel\System\Module\BaseModule');
        
        $this->assertFalse(isset($stub->new_value));
        $stub->new_value = 'foo bar';
        $this->assertTrue(isset($stub->new_value));
        $this->assertSame('foo bar', $stub->new_value);
    }
}
