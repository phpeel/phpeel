<?php
namespace Phpingguo\Tests\Phpingguo\Validator;

use Phpingguo\System\Validator\Options;

class ValidatorOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testInitOptions()
    {
        return function ($option_list, $is_reset) {
            $options	= Options::getInstance($is_reset);
            
            foreach ($option_list as $key => $value) {
                $options	= is_numeric($key) ? $options->$value() : $options->$key($value);
            }
            
            return $options;
        };
    }
    
    public function providerIsMethod()
    {
        return [
            [ true, [ 'whitespace', 'nullable' ], [ false, false, true, true, false, null, null, null, null, [] ] ],
            [ true, [ 'newLine', 'width', 'emoji' ], [ true, true, false, false, true, null, null, null, null, [] ] ],
            [ true, [ 'whitespace' ], [ false, false, false, true, false, null, null, null, null, [] ] ],
            [ false, [ 'width', 'emoji' ], [ true, false, false, true, true, null, null, null, null, [] ] ],
            [ true, [ 'min' => 0, 'max' => 10 ], [ false, false, false, false, false, 0, 10, null, null, [] ] ],
            [ false, [ 'preg' => 'aaa' ], [ false, false, false, false, false, 0, 10, 'aaa', null, [] ] ],
            [ false, [ 'notPreg' => 'bbb' ], [ false, false, false, false, false, 0, 10, 'aaa', 'bbb', [] ] ],
            [ true, [ 'whitespace' ], [ false, false, false, true, false, null, null, null, null, [] ] ],
            [ false, [ 'min' => -10, 'max' => 1 ], [ false, false, false, true, false, -10, 1, null, null, [] ] ],
            [ true, [ 'ngword' => [ 'hoge' ] ], [ false, false, false, false, false, null, null, null, null, [ 'hoge' ] ] ],
        ];
    }
    
    /**
     * @depends testInitOptions
     * @dataProvider providerIsMethod
     */
    public function testIsMethod($is_reset, $option_list, $check_list, $init)
    {
        $options	= $init($option_list, $is_reset);
        
        $this->assertSame($check_list[0], $options->isAllowEmoji());
        $this->assertSame($check_list[1], $options->isNewLine());
        $this->assertSame($check_list[2], $options->isNullable());
        $this->assertSame($check_list[3], $options->isWhiteSpace());
        $this->assertSame($check_list[4], $options->isWidth());
        $this->assertSame($check_list[5], $options->getMinValue());
        $this->assertSame($check_list[6], $options->getMaxValue());
        $this->assertSame($check_list[7], $options->getPregValue());
        $this->assertSame($check_list[8], $options->getNotPregValue());
        $this->assertSame($check_list[9], $options->getNgWordList());
    }
}
