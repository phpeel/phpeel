<?php
namespace Phpeel\System\Tests\Generator\Html;

use Phpeel\System\Enums\TemplateEngine;
use Phpeel\System\Generator\Html\ContentGenerator;
use Phpeel\System\Request\Request;
use Phpeel\System\Variable\Client;
use Phpeel\System\Variable\Server;

class ContentGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $generator = new ContentGenerator(
            [
                'TWIG'   =>
                    [
                        'class'   => 'Phpeel\System\Generator\Html\Engine\TwigProxy',
                        'options' =>
                            [
                                'DebugMode'  => true,
                                'Recompile'  => true,
                                'AutoEscape' => true,
                            ]
                    ],
                'SMARTY' =>
                    [
                        'class'   => 'Phpeel\System\Generator\Html\Engine\SmartyProxy',
                        'options' =>
                            [
                                'DebugMode'      => true,
                                'LeftDelimiter'  => '{{',
                                'RightDelimiter' => '}}',
                            ]
                    ]
            ]
        );

        $this->assertInstanceOf('Phpeel\System\Generator\Html\ContentGenerator', $generator);
        
        return $generator;
    }

    public function testBuildProvider()
    {
        return [
            [ TemplateEngine::TWIG ],
            [ TemplateEngine::SMARTY ],
        ];
    }
    
    /**
     * @dataProvider testBuildProvider
     * @depends testInit
     */
    public function testBuild($engine, ContentGenerator $generator)
    {
        $_SERVER = [ Server::REQUEST_METHOD => 'GET' ];
        Server::capture();
        Client::capture();
        
        /** @var \Phpeel\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpeel\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->getModuleData()->setEngine($engine);
        
        $this->assertNotEmpty($generator->build($stub, []));
    }
}
