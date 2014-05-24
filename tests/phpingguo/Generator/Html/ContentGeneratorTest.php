<?php
namespace Phpingguo\System\Tests\Generator\Html;

use Phpingguo\System\Enums\TemplateEngine;
use Phpingguo\System\Generator\Html\ContentGenerator;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Variable\Client;
use Phpingguo\System\Variable\Server;

class ContentGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $generator = new ContentGenerator(
            [
                'TWIG'   =>
                    [
                        'class'   => 'Phpingguo\System\Generator\Html\Engine\TwigProxy',
                        'options' =>
                            [
                                'DebugMode'  => true,
                                'Recompile'  => true,
                                'AutoEscape' => true,
                            ]
                    ],
                'SMARTY' =>
                    [
                        'class'   => 'Phpingguo\System\Generator\Html\Engine\SmartyProxy',
                        'options' =>
                            [
                                'DebugMode'      => true,
                                'LeftDelimiter'  => '{{',
                                'RightDelimiter' => '}}',
                            ]
                    ]
            ]
        );

        $this->assertInstanceOf('Phpingguo\System\Generator\Html\ContentGenerator', $generator);
        
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
        
        /** @var \Phpingguo\System\Module\BaseModule $stub */
        $stub = $this->getMockForAbstractClass(
            'Phpingguo\System\Module\BaseModule',
            [ Request::getInstance(true) ]
        );
        $stub->getModuleData()->setEngine($engine);
        
        $this->assertNotEmpty($generator->build($stub, []));
    }
}
