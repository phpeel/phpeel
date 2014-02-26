<?php
namespace Phpingguo\Tests\Phpingguo\Exts\Lib;

use Phpingguo\System\Enums\EnumFullName;
use Phpingguo\System\Exts\Lib\EnumClassGenerator;
use Phpingguo\System\Enums\Validator;
use Phpingguo\System\Enums\Variable;

class EnumClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [ EnumFullName::VALIDATOR, Validator::INTEGER ],
            [ EnumFullName::VALIDATOR, Validator::ALPHABET ],
            [ EnumFullName::VALIDATOR, Validator::ASCII ],
            [ EnumFullName::VARIABLE, Variable::INTEGER ],
            [ EnumFullName::VARIABLE, Variable::FLOAT ],
            [ EnumFullName::VARIABLE, Variable::STRING ],
        ];
    }
    
    /**
     * @dataProvider provider
     */
    public function test($enum_name, $enum_value)
    {
        $enum_datas	= EnumClassGenerator::done($enum_name, $enum_value);
        
        $this->assertInstanceOf($enum_name, $enum_datas[0]);
        $this->assertInstanceOf($enum_value, $enum_datas[1]);
        $this->assertSame($enum_value, $enum_datas[2]);
    }
}
