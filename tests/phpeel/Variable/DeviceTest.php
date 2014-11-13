<?php
namespace Phpeel\System\Tests\Variable;

use Phpeel\System\Variable\Device;
use Phpeel\System\Variable\Server;

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
    
    public function providerUserAgents()
    {
        return [
            [ 'DoCoMo/1.0/D502i/c10', 'DoCoMo', null, 'FeaturePhone' ],
            [ 'KDDI-HI31 UP.Browser/6.2.0.5 (GUI) MMP/2.0', 'AU', null, 'FeaturePhone' ],
            [ 'UP.Browser/3.01-HI02 UP.Link/3.2.1.2', 'AU', null, 'FeaturePhone' ],
            [ 'MOT-V980/x.x.x MIB/2.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1', 'SoftBank', null, 'FeaturePhone' ],
            [ 'Vodafone/1.0/V702NK/NKJ001', 'SoftBank', null, 'FeaturePhone' ],
            [ 'J-PHONE/4.3/V603T', 'SoftBank', null, 'FeaturePhone' ],
            [ 'SoftBank/1.0/DM001SH/SHJ001/SN', 'SoftBank', null, 'FeaturePhone' ],
            [ 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en)', 'iPhone', false, 'SmartPhone' ],
            [ 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp)', 'iPhone', '2_0', 'SmartPhone' ],
            [ 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)', 'iPhone', '5_0_1', 'SmartPhone' ],
            [ 'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us)', 'iPad', '3_2', 'SmartPhone' ],
            [ 'Mozilla/5.0 (iPad; CPU OS 5_0_1 like Mac OS X)', 'iPad', '5_0_1', 'SmartPhone' ],
            [ 'Mozilla/5.0 (Linux; U; Android 1.5; ja-jp; GDDJ-09 Build/CDB56)', 'Android', '1.5', 'SmartPhone' ],
            [ 'Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03S)', 'Android', '4.1.1', 'SmartPhone' ],
            [
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; KDDI-TS01; Windows Phone 6.5.3.5)',
                'WindowsPhone',
                null,
                'SmartPhone'
            ],
            [ 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5;', 'WindowsPhone', null, 'SmartPhone' ],
            [ 'BlackBerry9000/4.6.0.294 Profile/MIDP-2.0', 'BlackBerry', null, 'SmartPhone' ],
            [ 'Mozilla/5.0 (BlackBerry; U; BlackBerry 9700; ja) AppleWebKit/534.8+', 'BlackBerry', null, 'SmartPhone' ],
            [ 'Mozilla/5.0 (Symbian/3; Series60/5.2 NokiaN8-00/013.016;', 'Symbian', null, 'Undefined' ],
            [ 'Nokia6600/1.0 (4.03.24) SymbianOS/6.1 Series60/2.0', 'Symbian', null, 'Undefined' ],
            [ 'Mozilla/5.0 (SymbianOS/9.1; U; [en]; SymbianOS/91 Series60/3.0)', 'Symbian', null, 'Undefined' ],
            [ 'Mozilla/5.0 (unittest) Gecko/20041202 Firefox/1.0', 'MozillaFirefox', '1.0', 'OtherDevice' ],
            [ 'Mozilla/5.0 (unittest) Gecko/20050223 Firefox/1.0.1', 'MozillaFirefox', '1.0.1', 'OtherDevice' ],
            [ 'Mozilla/5.0 (unittest) Gecko/20060111 Firefox/1.5.0.1', 'MozillaFirefox', '1.5.0.1', 'OtherDevice' ],
            [ 'Mozilla/5.0 (unittest) Gecko/20100101 Firefox/9.0.1', 'MozillaFirefox', '9.0.1', 'OtherDevice' ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/532.0 (ut) Chrome/3.0.195.38 Safari/532.0',
                'GoogleChrome',
                '3.0.195.38',
                'OtherDevice' ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/534.10 (ut) Chrome/8.0.552.224 Safari/534.10 ChromePlus/1.5.2.0',
                'GoogleChrome',
                '8.0.552.224',
                'OtherDevice'
            ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/534.24 (ut) Chrome/11.0.696.71 Safari/534.24',
                'GoogleChrome',
                '11.0.696.71',
                'OtherDevice'
            ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/534.30 (ut) Chrome/12.0.742.112 Safari/534.30',
                'GoogleChrome',
                '12.0.742.112',
                'OtherDevice'
            ],
            [ 'Mozilla/5.0 (ut) AppleWebKit/85.7 (ut) Safari/85.6', 'Safari', '85.6', 'OtherDevice' ],
            [ 'Mozilla/5.0 (ut) AppleWebKit/312.1 (ut) Safari/312', 'Safari', '312', 'OtherDevice' ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/522.11.1 (ut) Version/3.0.3 Safari/522.12.1',
                'Safari',
                '522.12.1',
                'OtherDevice'
            ],
            [
                'Mozilla/5.0 (ut) AppleWebKit/531.21.11 (ut) Version/4.0.4 Safari/531.21.10',
                'Safari',
                '531.21.10',
                'OtherDevice'
            ],
        ];
    }

    /**
     * @dataProvider providerUserAgents
     */
    public function testUserAgents($user_agent, $ua_expected, $ver_expected, $ct_expected)
    {
        $_SERVER['HTTP_USER_AGENT'] = $user_agent;
        Server::capture();
        $this->assertSame($ua_expected, Device::getClientType());
        $this->assertSame($ver_expected, Device::getClientVersion());
        $this->assertSame($ct_expected, Device::getClientCategory());
    }
}
