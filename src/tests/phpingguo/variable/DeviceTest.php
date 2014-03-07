<?php
namespace Phpingguo\Tests\Phpingguo\Variable;

use Phpingguo\System\Variable\Device;
use Phpingguo\System\Variable\Server;

class DeviceTest extends \PHPUnit_Framework_TestCase
{
    public function providerGetClientIp()
    {
        return [
            [ 'REMOTE_ADDR', '127.0.0.1', null, true, '127.0.0.1' ],
            [ 'REMOTE_ADDR', '::1', null, true, '::1' ],
            [ 'HTTP_X_FORWARDED_FOR', '192.168.1.1', '127.0.0.1', false, '192.168.1.1' ],
            [ 'HTTP_X_FORWARDED_FOR', 'fc00::', '::1', false, 'fc00::' ],
            [ 'HTTP_X_FORWARDED_FOR', '192.168.1.1', '127.0.0.1', true, '127.0.0.1' ],
            [ 'HTTP_X_FORWARDED_FOR', 'fc00::', '::1', true, '::1' ],
        ];
    }

    /**
     * @dataProvider providerGetClientIp
     */
    public function testGetClientIp($server_value, $ip_address, $default_ip, $ignore_proxy, $expected)
    {
        isset($default_ip) && $_SERVER['REMOTE_ADDR'] = $default_ip;
        $_SERVER[$server_value] = $ip_address;
        Server::capture();
        $this->assertSame($expected, Device::getClientIp($ignore_proxy));
    }
    
    public function providerGetClientType()
    {
        return [
            [ 'DoCoMo/1.0/D502i/c10', 'DoCoMo' ],
            [ 'KDDI-HI31 UP.Browser/6.2.0.5 (GUI) MMP/2.0', 'AU' ],
            [ 'UP.Browser/3.01-HI02 UP.Link/3.2.1.2', 'AU' ],
            [ 'MOT-V980/x.x.x MIB/2.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1', 'SoftBank' ],
            [ 'Vodafone/1.0/V702NK/NKJ001', 'SoftBank' ],
            [ 'J-PHONE/4.3/V603T', 'SoftBank' ],
            [ 'SoftBank/1.0/DM001SH/SHJ001/SN', 'SoftBank' ],
            [ 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en)', 'iPhone' ],
            [ 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp)', 'iPhone' ],
            [ 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)', 'iPhone' ],
            [ 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us)', 'iPad' ],
            [ 'Mozilla/5.0 (iPad; CPU OS 5_0_1 like Mac OS X)', 'iPad' ],
            [ 'Mozilla/5.0 (Linux; U; Android 1.5; ja-jp; GDDJ-09 Build/CDB56)', 'Android' ],
            [ 'Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03S)', 'Android' ],
            [ 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; KDDI-TS01; Windows Phone 6.5.3.5)', 'WindowsPhone' ],
            [ 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5;', 'WindowsPhone' ],
            [ 'BlackBerry9000/4.6.0.294 Profile/MIDP-2.0', 'BlackBerry' ],
            [ 'Mozilla/5.0 (BlackBerry; U; BlackBerry 9700; ja) AppleWebKit/534.8+', 'BlackBerry' ],
            [ 'Mozilla/5.0 (Symbian/3; Series60/5.2 NokiaN8-00/013.016;', 'Symbian' ],
            [ 'Nokia6600/1.0 (4.03.24) SymbianOS/6.1 Series60/2.0', 'Symbian' ],
            [ 'Mozilla/5.0 (SymbianOS/9.1; U; [en]; SymbianOS/91 Series60/3.0)', 'Symbian' ],
        ];
    }

    /**
     * @dataProvider providerGetClientType
     */
    public function testGetClientType($user_agent, $expected)
    {
        $_SERVER['HTTP_USER_AGENT'] = $user_agent;
        Server::capture();
        $this->assertSame($expected, Device::getClientType());
    }
}
