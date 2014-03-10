<?php
namespace Phpingguo\Tests\Core;

use Phpingguo\System\Core\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [ [ 'test.first' => 'hogehoge' ], [ 'test' => [ 'first' => 'hogehoge' ] ], true ],
            [ [ 'test.first' => null ], [  ], false ],
        ];
    }
    
    /**
     * @dataProvider provider
     */
    public function test($set_values, $expected, $is_set)
    {
        Config::init();
        
        if ($is_set === true) {
            foreach ($set_values as $key => $value) {
                Config::set($key, $value);
            }
        }
        
        foreach ($set_values as $key => $value) {
            $this->assertSame($value, Config::get($key));
        }
        
        $this->assertSame($expected, Config::getAll());
    }
}
