<?php
namespace Phpingguo\System\Tests\Generator\Xml;

use Phpingguo\System\Generator\Xml\ContentGenerator;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Variable\Client;
use Phpingguo\System\Variable\Server;

class ContentGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $generator = new ContentGenerator();
        $options = [
            'SuperParentName'     => 'xml_body',
            'DefaultListItemName' => 'list_item',
        ];
        
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
        Server::capture();
        Client::capture();
        
        /** @var \Phpingguo\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpingguo\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->name = 'test';
        $stub->hierarchies = [ [ 'name' => 'test2' ], [ 'name' => 'test3' ] ];
        $stub->list = [ 'name' => 'test4' ];
        
        $this->assertNotEmpty($generator->build($stub, $options));
    }
}
