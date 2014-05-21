<?php
namespace Phpingguo\System\Tests\Generator;

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
            [ ContentType::HTML, [] ],
            [ ContentType::JSON, [] ],
        ];
    }
    
    /**
     * @dataProvider testBuildContentProvider
     * @depends testInit
     */
    public function testBuildContent($content_type, array $options, GeneratorProxy $generator)
    {
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
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
