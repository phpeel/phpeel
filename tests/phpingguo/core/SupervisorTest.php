<?php
namespace Phpingguo\Tests\Core;

use Phpingguo\System\Core\Supervisor;

class SupervisorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $root_path    = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
        );
        $project_path = $root_path . DIRECTORY_SEPARATOR . 'src';
        $app_path     = $project_path . DIRECTORY_SEPARATOR . 'app';
        
        $this->assertSame($project_path, Supervisor::getProjectPath());
        $this->assertSame($project_path . DIRECTORY_SEPARATOR . 'phpingguo', Supervisor::getSystemPath());
        $this->assertSame($app_path, Supervisor::getAppPath());
        $this->assertSame($app_path . DIRECTORY_SEPARATOR . 'config', Supervisor::getConfigPath());
        $this->assertSame($app_path . DIRECTORY_SEPARATOR . 'cache', Supervisor::getCachePath());
    }
}
