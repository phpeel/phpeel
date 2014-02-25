<?php
use Phpingguo\System\Core\Client;
use Phpingguo\System\Core\Config;
use Phpingguo\System\Core\Server;
use Phpingguo\System\Enums\HttpMethod;
use Phpingguo\System\Enums\Validator;
use Phpingguo\System\Enums\Variable;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Validator\Options;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testInitOptions()
    {
        return function ($option_list) {
            $options	= Options::getInstance(true);
            
            foreach ($option_list as $method) {
                $options	= $options->$method();
            }
            
            return $options;
        };
    }
    
    public function providerRequest()
    {
        return [
            [ HttpMethod::GET, '/top/index', [], [ HttpMethod::GET, 'top', 1.0, 'index', [] ] ],
        ];
    }
    
    /**
     * @dataProvider providerRequest
     */
    public function testRequest($method, $path_info, $params, $expected_list)
    {
        $_SERVER['REQUEST_METHOD']	= $method;
        
        isset($path_info) && $_SERVER['PATH_INFO'] = $path_info;
        count($params) > 0 && $_REQUEST = $params;
        
        Server::capture();
        Client::capture();
        
        $instance	= Request::getInstance(true);
        
        $this->assertInstanceOf('Phpingguo\System\Request\RequestData', $instance->getRequestData());
        $this->assertSame($expected_list[0], $instance->getRequestData()->getMethod()->getValue());
        $this->assertSame($expected_list[1], $instance->getModuleName());
        $this->assertSame($expected_list[2], $instance->getApiVersion());
        $this->assertSame($expected_list[3], $instance->getSceneName());
        $this->assertSame($expected_list[4], $instance->getParameters());
    }
    
    public function providerControlParameter()
    {
        return [
            [ Variable::STRING, Validator::TEXT, [ 'whitespace' ], 'name', '緒方 智絵里', null, null ],
            [ Variable::STRING, Validator::TEXT, [ 'whitespace' ], '397cherry', [ '前川 みく', '安部 菜々', '緒方 智絵里' ], null, null ],
            [ Variable::STRING, Validator::TEXT, [ 'whitespace' ], 'name2', '緒方 智絵里', null, 'LogicException' ],
        ];
    }
    
    /**
     * @depends testInitOptions
     * @dataProvider providerControlParameter
     */
    public function testControlParameter($type, $v_type, $options, $key, $value, $expected, $exception, $init)
    {
        global $__CONFIG;
        
        $__CONFIG	= [];
        $_SERVER['REQUEST_METHOD']	= HttpMethod::GET;
        $_SERVER['PATH_INFO']		= '/top/index';
        
        isset($exception) && $this->setExpectedException($exception);
        
        Server::capture();
        Client::capture();
        
        $instance	= Request::getInstance(true);
        $instance->setParameter($type, $key, $value);
        
        isset($exception) || $this->assertTrue($instance->validate($v_type, $key, $init($options)));
        $this->assertTrue($instance->isExistParam($key));
        $this->assertSame($expected ?: $value, $instance->getParameter($type, $key));
    }
    
    public function providerAllParameters()
    {
        return [
            [ '397cherry', [ '397cherry' => [ '前川 みく', '安部 菜々', '緒方 智絵里' ] ] ]
        ];
    }
    
    /**
     * @dataProvider providerAllParameters
     */
    public function testAllParameters($key, $params)
    {
        global $__CONFIG;
        
        $__CONFIG	= [];
        $_SERVER['REQUEST_METHOD']	= HttpMethod::GET;
        $_SERVER['PATH_INFO']		= '/top/index';
        
        Config::set('sys.security.validation_forced', false);
        Server::capture();
        Client::capture();
        
        $instance	= Request::getInstance(true);
        $instance->setParameters($params);
        
        $this->assertSame($params, $instance->getParameters());
        $this->assertArrayHasKey($key, $instance->getParameters());
    }
    
    public function providerValidate()
    {
        return [
            [ Variable::TEXT, Validator::TEXT, 'v_name1', '緒方 智絵里', [], [], null ],
            [ Variable::TEXT, Validator::TEXT, 'v_name2', '緒方 智絵里', [ 'whitespace' ], true, null ],
            [ Variable::TEXT, Validator::TEXT, 'v_name3', null, [], [], 'RuntimeException' ],
            [ Variable::TEXT, Validator::TEXT, 'v_name4', [ '前川 みく', '安部 菜々', '緒方 智絵里' ], [], [], null ],
            [ Variable::TEXT, Validator::TEXT, 'v_name5', [ '前川 みく', '安部 菜々', '緒方 智絵里' ], [ 'whitespace' ], true, null ],
            [ Variable::TEXT, Validator::TEXT, 'v_name6', [ '前川 みく', '', '緒方 智絵里' ], [ 'whitespace', 'nullable' ], false, null ],
            [ Variable::TEXT, Validator::TEXT, 'v_name7', [ '' ], [ 'nullable' ], false, null ],
        ];
    }
    
    /**
     * @depends testInitOptions
     * @dataProvider providerValidate
     */
    public function testValidate($p_type, $type, $name, $value, $options, $expected, $exception, $init)
    {
        $_SERVER['REQUEST_METHOD']	= HttpMethod::GET;
        $_SERVER['PATH_INFO']		= '/top/index';
        
        Server::capture();
        Client::capture();
        
        isset($exception) && $this->setExpectedException($exception);
        
        $instance	= Request::getInstance(true);
        isset($value) && $instance->setParameter($p_type, $name, $value);
        
        $result	= $instance->validate($type, $name, $init($options));
        
        if (is_array($expected))
        {
            $this->assertInternalType('array', $result);
        }
        else
        {
            $this->assertSame($expected, $result);
        }
    }
    
    public function providerMultiValidate()
    {
        return [
            [ [ Validator::TEXT, 'mv_name1', [ 'whitespace' ] ], [ 'mv_name1' => '安部 菜々' ], null ],
            [ [ Validator::TEXT, 'mv_name2', [] ], [ 'mv_name2' => '安部 菜々' ], null ],
            [ [ Validator::TEXT, 'mv_name3', [ 'nullable' ] ], [ 'mv_name3' => '' ], null ],
            [ [], [], 'InvalidArgumentException'],
            [ [ Validator::TEXT, 'mv_name4', [], Validator::TEXT ], [], 'InvalidArgumentException'],
            [ [ Validator::TEXT, 'mv_name5_1', [  ], Validator::TEXT, 'mv_name5_2', [  ] ], [ 'mv_name5_1' => '安部 菜々', 'mv_name5_2' => '緒方 智絵里' ], null ],
        ];
    }
    
    /**
     * @depends testInitOptions
     * @dataProvider providerMultiValidate
     */
    public function testMultiValidate($validates, $params, $exception, $init)
    {
        $_SERVER['REQUEST_METHOD']	= HttpMethod::GET;
        $_SERVER['PATH_INFO']		= '/top/index';
        
        Server::capture();
        Client::capture();
        
        isset($exception) && $this->setExpectedException($exception);
        
        $instance	= Request::getInstance(true);
        
        for ($i = 2, $length = count($validates); $i < $length; $i += 3)
        {
            $validates[$i] = $init($validates[$i]);
        }
        
        isset($params) && $instance->setParameters($params);
        call_user_func_array(array($instance, 'multipleValidate'), $validates);
    }
}
