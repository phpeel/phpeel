<?php
namespace Phpingguo\Tests\Variable;

use Phpingguo\System\Variable\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [ [ Server::SRV_ENV_NAME => 'local' ], [ Server::SRV_ENV_NAME => 'SRV_ENV_NAME' ], true ],
            [ [ Server::SRV_ENV_NAME => null ], [ Server::SRV_ENV_NAME => 'SRV_ENV_NAME_2' ], false ],
        ];
    }
    
    /**
     * @dataProvider provider
     */
    public function test($server_vals, $call_statics, $registry)
    {
        ($registry === true) && $_SERVER = $server_vals;
        
        Server::capture();
        
        foreach ($server_vals as $key => $value) {
            $this->assertSame($value, Server::getValue($key));
            $this->assertSame($value, Server::$call_statics[$key]($key));
        }
    }
}
