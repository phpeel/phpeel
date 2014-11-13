<?php
namespace Phpeel\System\Tests\Generator;

use Phpeel\System\Core\Config;
use Phpeel\System\Enums\ContentType;
use Phpeel\System\Generator\GeneratorProxy;
use Phpeel\System\Request\Request;
use Phpeel\System\Variable\Client;
use Phpeel\System\Variable\Server;

class GeneratorProxyTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $instance = GeneratorProxy::getInstance();
        
        $this->assertInstanceOf('Phpeel\System\Generator\GeneratorProxy', $instance);
        
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
        
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpeel\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->getResponse()->setContentType($content_type);
        
        $this->assertNotEmpty($generator->buildContent($stub, $options));
    }
}
