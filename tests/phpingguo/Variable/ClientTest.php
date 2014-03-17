<?php
namespace Phpingguo\System\Tests\Variable;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Enums\HttpMethod;
use Phpingguo\System\Variable\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [ HttpMethod::GET, [ 'name' => '緒方 智絵里' ], [ 'name' => '緒方 智絵里' ], [], [ 'name' => '緒方 智絵里' ], true ],
            [ HttpMethod::GET, [ 'name' => '緒方 智絵里' ], [ 'name' => '緒方 智絵里' ], [], [ 'name' => '緒方 智絵里' ], false ],
            [ HttpMethod::POST, [ 'name' => '安部 菜々' ], [], [ 'name' => '安部 菜々' ], [ 'name' => '安部 菜々' ], true ],
            [ HttpMethod::POST, [ 'name' => '安部 菜々' ], [], [ 'name' => '安部 菜々' ], [ 'name' => '安部 菜々' ], false ],
            [ HttpMethod::HEAD, [ 'name' => '前川 みく' ], [], [], [ 'name' => '前川 みく' ], true ],
            [ HttpMethod::HEAD, [ 'name' => '前川 みく' ], [], [], [ 'name' => '前川 みく' ], false ],
        ];
    }
    
    /**
     * @dataProvider provider
     */
    public function test($method, $request, $get, $post, $expected, $use_request)
    {
        $_REQUEST = $request;
        $_GET     = $get;
        $_POST    = $post;
        
        Config::set('sys.security.use_requests', $use_request);
        Client::capture();
        
        $this->assertSame($expected, Client::getParameters($method));
    }
}
