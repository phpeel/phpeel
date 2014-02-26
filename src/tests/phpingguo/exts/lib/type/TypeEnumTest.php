<?php
namespace Phpingguo\Tests\Phpingguo\Exts\Lib\Type;

use Phpingguo\System\Enums\EnumFullName;
use Phpingguo\System\Enums\Validator;
use Phpingguo\System\Enums\Variable;

class TypeEnumTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [ EnumFullName::CHARSET, 'ASCII', 'ASCII', null ],
            [ EnumFullName::CHARSET, 'UTF8', 'UTF-8', null ],
            [ EnumFullName::CHARSET, 'EUC_JP', 'EUC-JP', 'InvalidArgumentException' ],
            [ EnumFullName::HTTP_METHOD, 'GET', 'GET', null ],
            [ EnumFullName::HTTP_METHOD, 'POST', 'POST', null ],
            [ EnumFullName::HTTP_METHOD, 'HOGEHOGE', 'HOGEHOGE', 'InvalidArgumentException' ],
            [ EnumFullName::VALIDATION_ERROR, 'INVALID', 'null', null ],
            [ EnumFullName::VALIDATION_ERROR, 'MIN', 'min', null ],
            [ EnumFullName::VALIDATION_ERROR, 'FOO', 'foo', 'InvalidArgumentException' ],
            [ EnumFullName::VALIDATOR, 'INTEGER', Validator::INTEGER, null ],
            [ EnumFullName::VALIDATOR, 'ALPHABET', Validator::ALPHABET, null ],
            [ EnumFullName::VALIDATOR, 'BAR', 'BAR', 'InvalidArgumentException' ],
            [ EnumFullName::VARIABLE, 'INTEGER', Variable::INTEGER, null ],
            [ EnumFullName::VARIABLE, 'STRING', Variable::STRING, null ],
            [ EnumFullName::VARIABLE, 'VECTOR', 'VECTOR', 'InvalidArgumentException' ],
        ];
    }
    
    /**
     * @dataProvider provider
     */
    public function test($enum_name, $key, $value, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $this->assertSame($value, (new $enum_name($value))->getValue());
        $this->assertSame($value, (string)(new $enum_name($value)));
        $this->assertSame($value, $enum_name::{$key}()->getValue());
        $this->assertSame($value, (string)$enum_name::{$key}());
        $this->assertSame($value, $enum_name::init($value)->getValue());
        $this->assertSame($value, (string)$enum_name::init($value));
    }
    
    public function providerInitMethod()
    {
        return [
            [ EnumFullName::VALIDATOR, Validator::INTEGER(), Validator::INTEGER ],
            [ EnumFullName::VALIDATOR, new Validator(Validator::INTEGER), Validator::INTEGER ],
        ];
    }
    
    /**
     * @dataProvider providerInitMethod
     */
    public function testInitMethod($enum_name, $instance, $expected)
    {
        $this->assertSame($expected, $enum_name::init($instance)->getValue());
        $this->assertSame($expected, (string)$enum_name::init($instance));
    }
}
