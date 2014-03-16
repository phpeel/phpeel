<?php
namespace Phpingguo\System\Tests\Variable;

use Phpingguo\System\Variable\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function providerPropertyAccess()
    {
        return [
            [ 'name', 'foo bar', 'foo bar' ],
        ];
    }

    /**
     * @dataProvider providerPropertyAccess
     */
    public function testPropertyAccess($key, $value, $expected)
    {
        Session::getInstance()->open();
        
        $this->assertNull(Session::getInstance()->get($key));
        $this->assertTrue(Session::getInstance()->set($key, $value));
        $this->assertTrue(Session::getInstance()->isExist($key));
        $this->assertSame($expected, Session::getInstance()->get($key, $value));
        $this->assertArrayHasKey($key, Session::getInstance()->getAll());
        $this->assertTrue(Session::getInstance()->remove($key));
        $this->assertFalse(Session::getInstance()->isExist($key));
        
        Session::getInstance()->close();
    }
}
