<?php
namespace Phpingguo\System\Tests\Request;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Enums\HttpMethod;
use Phpingguo\System\Request\RequestParser;
use Phpingguo\System\Variable\Client;
use Phpingguo\System\Variable\Server;

class RequestParserTest extends \PHPUnit_Framework_TestCase
{
    public function providerParseRequest()
    {
        return [
            [ HttpMethod::GET, '/top/index', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/top/index', [], [ 1.0, 'top', 'index' ], false, false, null ],
            [ HttpMethod::HEAD, '/top/index', [], [ 1.0, 'top', 'index' ], false, false, null ],
            [ HttpMethod::HEAD, '/top/index', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [
                HttpMethod::GET,
                '/top/index',
                [ 'idols' => [ '前川 みく', '安部 菜々', '緒方 智絵里' ] ],
                [ 1.0, 'top', 'index' ],
                false,
                true,
                null
            ],
            [
                HttpMethod::HEAD,
                '/top/index',
                [ 'idols' => [ '前川 みく', '安部 菜々', '緒方 智絵里' ] ],
                [ 1.0, 'top', 'index' ],
                false,
                false,
                null
            ],
            [ HttpMethod::GET, '/TOP/INDEX', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/top', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/top/', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/TOP', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/TOP/', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/', [], [ 1.0, 'top', 'index' ], false, true, null ],
            [ HttpMethod::GET, '/v1.0/top/index', [], [ 1.0, 'top', 'index' ], true, true, null ],
            [ HttpMethod::GET, '/a/b/c/d', [], [], false, true, 'RuntimeException' ],
            [ HttpMethod::GET, '/v100/top/index', [], [], true, true, 'RuntimeException' ],
            [ HttpMethod::GET, '/v1.0/top/index', [], [], false, true, 'LogicException' ],
        ];
    }
    
    /**
     * @dataProvider providerParseRequest
     */
    public function testParseRequest($method, $path_info, $params, $expects, $versioning, $use_request, $exception)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        
        isset($path_info) && $_SERVER['PATH_INFO'] = $path_info;
        isset($exception) && $this->setExpectedException($exception);
        count($params) > 0 && $_REQUEST = $params;
        
        Config::set('sys.versioning.allowed', $versioning);
        Config::set('sys.security.use_requests', $use_request);
        Server::capture();
        Client::capture();
        
        $result = RequestParser::getInstance(true)->get();
        
        $this->assertInstanceOf('Phpingguo\System\Request\RequestData', $result);
        count($params) > 0 && $this->assertArrayNotHasKey('idols', $result->getParameters());
        $this->assertSame($expects[0], $result->getApiVersion());
        $this->assertSame($expects[1], $result->getModuleName());
        $this->assertSame($expects[2], $result->getSceneName());
    }
}
