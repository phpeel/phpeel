<?php
namespace Phpeel\System\Tests\Core;

use Phpeel\System\Core\Supervisor;

class SupervisorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $root_path    = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
        );
        $project_path = $root_path . DIRECTORY_SEPARATOR . 'src';
        $app_path     = $project_path . DIRECTORY_SEPARATOR . 'app';
        $config_path  = $project_path . DIRECTORY_SEPARATOR . 'config';
        $srv_env_path = $config_path . DIRECTORY_SEPARATOR . 'server_environments';
        
        $this->assertSame($project_path, Supervisor::getProjectPath());
        $this->assertSame($project_path . DIRECTORY_SEPARATOR . 'phpeel', Supervisor::getSystemPath());
        $this->assertSame($config_path, Supervisor::getConfigPath());
        $this->assertSame($srv_env_path . DIRECTORY_SEPARATOR . 'local', Supervisor::getServerEnvPath());
        $this->assertSame($app_path, Supervisor::getAppPath());
        $this->assertSame($app_path . DIRECTORY_SEPARATOR . 'View', Supervisor::getViewPath());
        $this->assertSame($app_path . DIRECTORY_SEPARATOR . 'Cache', Supervisor::getCachePath());
    }
}
