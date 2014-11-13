<?php
namespace Phpeel\System\Tests\Module;

use Phpeel\System\Enums\TemplateEngine;
use Phpeel\System\Module\ModuleData;
use Phpeel\System\Request\Request;
use Phpeel\System\Variable\Client;
use Phpeel\System\Variable\Server;

class ModuleDataTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultInitialize()
    {
        $data = new ModuleData();
        
        $this->assertNull($data->getRequest());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $data->getResponse());
        $this->assertNull($data->getModuleName());
        $this->assertNull($data->getSceneName());
        $this->assertSame(TemplateEngine::TWIG, $data->getEngine()->getValue());
        $this->assertCount(1, $data->getVariables());
        $this->assertArrayHasKey('response', $data->getVariables());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $data->getVariable('response'));
    }
    
    public function testOptionalInitialize()
    {
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
        Server::capture();
        Client::capture();
        
        $data = new ModuleData(Request::getInstance(true), TemplateEngine::SMARTY);
        
        $this->assertInstanceOf('Phpeel\System\Request\Request', $data->getRequest());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $data->getResponse());
        $this->assertSame(TemplateEngine::SMARTY, $data->getEngine()->getValue());
    }
    
    public function providerPropertiesSuccess()
    {
        return [
            [
                [ 'module' => 'test' ],
                [ 'test', null, TemplateEngine::TWIG ]
            ],
            [
                [ 'scene' => 'sub' ],
                [ null, 'sub', TemplateEngine::TWIG ]
            ],
            [
                [ 'engine' => TemplateEngine::SMARTY ],
                [ null, null, TemplateEngine::SMARTY ]
            ],
            [
                [ 'value' => [ 'name', 'foo bar' ] ],
                [ null, null, TemplateEngine::TWIG, 'foo bar' ]
            ],
            [
                [ 'values' => [ 'name' => 'hoge hoge' ] ],
                [ null, null, TemplateEngine::TWIG, 'hoge hoge' ]
            ]
        ];
    }

    /**
     * @dataProvider providerPropertiesSuccess
     */
    public function testPropertiesSuccess($actual, $expected)
    {
        $data = new ModuleData();
        
        array_key_exists('module', $actual) && $data->setModuleName($actual['module']);
        array_key_exists('scene', $actual) && $data->setSceneName($actual['scene']);
        array_key_exists('engine', $actual) && $data->setEngine($actual['engine']);
        array_key_exists('value', $actual) && $data->setVariable($actual['value'][0], $actual['value'][1]);
        array_key_exists('values', $actual) && $data->setVariables($actual['values']);
        
        $this->assertNull($data->getRequest());
        $this->assertInstanceOf('Phpeel\System\Response\Response', $data->getResponse());
        $this->assertSame($expected[0], $data->getModuleName());
        $this->assertSame($expected[1], $data->getSceneName());
        $this->assertSame($expected[2], $data->getEngine()->getValue());
        
        if (array_key_exists('value', $actual)) {
            $this->assertArrayHasKey($actual['value'][0], $data->getVariables());
            $this->assertSame($expected[3], $data->getVariable($actual['value'][0]));
        } elseif (array_key_exists('values', $actual)) {
            $this->assertArrayHasKey('name', $data->getVariables());
            $this->assertSame($expected[3], $data->getVariable('name'));
        }
    }
    
    public function providerPropertiesFailed()
    {
        return [
            [ null, null ],
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
    public function testSetModuleNameFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ModuleData();
        $data->setModuleName($actual);
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetSceneNameFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ModuleData();
        $data->setSceneName($actual);
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetEngineFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ModuleData();
        $data->setEngine($actual);
    }
    
    public function providerSetVariableFailed()
    {
        return [
            [ null, null ],
            [ '', null ],
            [ true, null ],
            [ false, null ],
            [ [], null ],
            [ [ 'a' ], null ],
            [ new \stdClass(), null ],
            [ 0, null ],
            [ 0.0, null ],
            [ 0.1, null ],
            [ '0', null ],
            [ '0.0', null ],
            [ '0.1', null ],
        ];
    }

    /**
     * @dataProvider providerSetVariableFailed
     */
    public function testSetVariableFailed($key, $value)
    {
        $data = new ModuleData();
        $data->setVariable($key, $value);
        $this->assertCount(1, $data->getVariables());
    }
}
