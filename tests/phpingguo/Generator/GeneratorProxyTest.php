<?php
namespace Phpingguo\System\Tests\Generator;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Enums\ContentType;
use Phpingguo\System\Generator\GeneratorProxy;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Variable\Client;
use Phpingguo\System\Variable\Server;

class GeneratorProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $instance = GeneratorProxy::getInstance();
        
        $this->assertInstanceOf('Phpingguo\System\Generator\GeneratorProxy', $instance);
        
        return $instance;
    }

    public function testBuildContentProvider()
    {
        return [
            [ ContentType::HTML, false, [] ],
            [ ContentType::HTML, true, [] ],
            [ ContentType::JSON, false, [] ],
            [ ContentType::JSON, true, [] ],
            [ ContentType::XML, false, [] ],
            [ ContentType::XML, true, [] ],
        ];
    }
    
    /**
     * @dataProvider testBuildContentProvider
     * @depends testInit
     */
    public function testBuildContent($content_type, $versioning, array $options, GeneratorProxy $generator)
    {
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
        ($versioning === true) && $_SERVER[Server::PATH_INFO] = '/v1.0/top/index';
        Config::set('sys.versioning.enabled', $versioning);
        Config::set('sys.versioning.strict_mode', $versioning);
        Server::capture();
        Client::capture();
        
        /** @var \Phpingguo\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpingguo\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->getResponse()->setContentType($content_type);
        
        $this->assertNotEmpty($generator->buildContent($stub, $options));
    }
}
