<?php
namespace Phpeel\System\Tests\Generator\Xml;

use Phpeel\System\Generator\Xml\ContentGenerator;
use Phpeel\System\Request\Request;
use Phpeel\System\Variable\Client;
use Phpeel\System\Variable\Server;

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
        
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpeel\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->name = 'test';
        $stub->hierarchies = [ [ 'name' => 'test2' ], [ 'name' => 'test3' ] ];
        $stub->list = [ 'name' => 'test4' ];
        
        $this->assertNotEmpty($generator->build($stub, $options));
    }
}
