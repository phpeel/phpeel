<?php
namespace Phpingguo\System\Tests\Response;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Response\AppApiUrl;

class AppApiUrlTest extends \PHPUnit_Framework_TestCase
{
    public function providerInitialize()
    {
        return [
            [
                [ 'module' => 'test', 'version' => null, 'scene' => null, 'params' => [] ],
                'test/index'
            ]
        ];
    }

    /**
     * @dataProvider providerInitialize
     */
    public function testInitialize($actual, $expected)
    {
        $instance = new AppApiUrl($actual['module'], $actual['version'], $actual['scene'], $actual['params']);
        
        $this->assertSame($expected, $instance->createUrl());
    }
    
    public function providerPropertiesSuccess()
    {
        return [
            [ [ 'module' => 'test' ], 'test/index', false ],
            [ [ 'module' => 'top', 'version' => 2.0 ], 'v2.0/top/index', true ],
            [ [ 'scene' => 'front' ], 'top/front', false ],
            [ [ 'params' => [ 'name' => 'foo' ] ], 'top/index?name=foo', false ],
            [
                [ 'params' => [ 'group' => [ 'foo', 'bar', 'hoge' ] ] ],
                'top/index?group[0]=foo&group[1]=bar&group[2]=hoge',
                false
            ]
        ];
    }

    /**
     * @dataProvider providerPropertiesSuccess
     */
    public function testPropertiesSuccess($actual, $expected, $is_versioning)
    {
        Config::set('sys.versioning.enabled', $is_versioning);
        
        $instance = new AppApiUrl();
        
        if (array_key_exists('module', $actual)) {
            if (array_key_exists('version', $actual)) {
                $instance->setModule($actual['module'], $actual['version']);
            } else {
                $instance->setModule($actual['module']);
            }
        }
        
        array_key_exists('scene', $actual) && $instance->setScene($actual['scene']);
        array_key_exists('params', $actual) && $instance->setParameters($actual['params']);
        
        $this->assertSame($expected, $instance->createUrl());
    }
    
    public function providerStrPropertiesFailed()
    {
        return [
            [ null, 'InvalidArgumentException' ],
            [ [], 'InvalidArgumentException' ],
            [ true, 'InvalidArgumentException' ],
            [ false, 'InvalidArgumentException' ],
            [ 0, 'InvalidArgumentException' ],
            [ 0.0, 'InvalidArgumentException' ],
            [ 0.1, 'InvalidArgumentException' ],
            [ '0', 'InvalidArgumentException' ],
            [ '0.0', 'InvalidArgumentException' ],
            [ '0.1', 'InvalidArgumentException' ],
            [ new \stdClass(), 'InvalidArgumentException' ],
        ];
    }

    /**
     * @dataProvider providerStrPropertiesFailed
     */
    public function testSetModuleFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new AppApiUrl();
        $instance->setModule($actual);
    }

    /**
     * @dataProvider providerStrPropertiesFailed
     */
    public function testSetSceneFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new AppApiUrl();
        $instance->setScene($actual);
    }
    
    public function providerVerPropertiesFailed()
    {
        return [
            [ null, null ],
            [ [], 'InvalidArgumentException' ],
            [ true, 'InvalidArgumentException' ],
            [ false, 'InvalidArgumentException' ],
            [ 0, 'InvalidArgumentException' ],
            [ 0.0, null ],
            [ 0.1, null ],
            [ '0', 'InvalidArgumentException' ],
            [ '0.0', 'InvalidArgumentException' ],
            [ '0.1', 'InvalidArgumentException' ],
            [ new \stdClass(), 'InvalidArgumentException' ],
        ];
    }

    /**
     * @dataProvider providerVerPropertiesFailed
     */
    public function testSetApiVersionFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new AppApiUrl();
        $instance->setModule('top', $actual);
    }
    
    public function providerArrayPropertiesFailed()
    {
        return [
            [ null, 'PHPUnit_Framework_Error' ],
            [ [], null ],
            [ true, 'PHPUnit_Framework_Error' ],
            [ false, 'PHPUnit_Framework_Error' ],
            [ 0, 'PHPUnit_Framework_Error' ],
            [ 0.0, 'PHPUnit_Framework_Error' ],
            [ 0.1, 'PHPUnit_Framework_Error' ],
            [ '0', 'PHPUnit_Framework_Error' ],
            [ '0.0', 'PHPUnit_Framework_Error' ],
            [ '0.1', 'PHPUnit_Framework_Error' ],
            [ new \stdClass(), 'PHPUnit_Framework_Error' ],
        ];
    }

    /**
     * @dataProvider providerArrayPropertiesFailed
     */
    public function testSetParamsFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $instance = new AppApiUrl();
        $instance->setParameters($actual);
    }
}
