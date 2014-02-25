<?php
use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\String\Other\MailAddress;

class ValidatorMailAddressTest extends PHPUnit_Framework_TestCase
{
    public function testInitOptions()
    {
        return function ($option_list) {
            $options	= Options::getInstance(true);
            
            foreach ($option_list as $key => $value) {
                $options	= is_numeric($key) ? $options->$value() : $options->$key($value);
            }
            
            return $options;
        };
    }
    
    public function providerValidate()
    {
        return [
            [ 'abcedf@xyz.com', true, [], null ],
            [ 'abc.edf.ghi@xyz.com', true, [], null ],
            [ 'ab-ced-fghi@xyz.com', true, [], null ],
            [ '_abcedfghi_@xyz.com', true, [], null ],
            [ '"ab..cd..ef"@xyz.com', true, [], null ],
            [ '"ab[cd]ef"@xyz.com', true, [], null ],
            [ '.@xyz.com', false, [], '\RuntimeException' ],
            [ '.a@xyz.com', false, [], '\RuntimeException' ],
            [ '.abc@xyz.com', false, [], '\RuntimeException' ],
            [ 'a.@xyz.com', false, [], '\RuntimeException' ],
            [ 'abc.@xyz.com', false, [], '\RuntimeException' ],
            [ 'ab..cd..ef@xyz.com', false, [], '\RuntimeException' ],
            [ 'abc...def@xyz.com', false, [], '\RuntimeException' ],
            [ 'abc....def@xyz.com', false, [], '\RuntimeException' ],
            [ '.ab..cd..ef.@xyz.com', false, [], '\RuntimeException' ],
            [ 'a[bcde]f@xyz.com', false, [], '\RuntimeException' ],
            [ 'a', false, [], 'ValidationErrorException' ],
            [ 'A', false, [], 'ValidationErrorException' ],
            [ 'abc', false, [ 'notPreg' => '/xyz/' ], 'ValidationErrorException' ],
            [ 'a b c', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ '', false, [ 'nullable' ], null ],
            [ null, false, [ 'nullable' ], null ],
            [ false, false, [ 'nullable' ], null ],
            [ [], false, [ 'nullable' ], null ],
            [ 'あいうえお', false, [], 'ValidationErrorException' ],
            [ 'ｱｲｳｴｵ', false, [], 'ValidationErrorException' ],
            [ 'アイウエオ', false, [], 'ValidationErrorException' ],
            [ 'a0', false, [], 'ValidationErrorException' ],
            [ 'abc', false, [ 'preg' => '/xyz/' ], 'ValidationErrorException' ],
            [ 0, false, [], 'ValidationErrorException' ],
            [ 0.0, false, [], 'ValidationErrorException' ],
            [ '0', false, [], 'ValidationErrorException' ],
            [ '0.0', false, [], 'ValidationErrorException' ],
            [ null, true, [], 'ValidationErrorException' ],
            [ '', true, [], 'ValidationErrorException' ],
            [ false, false, [], 'ValidationErrorException' ],
            [ [], false, [], 'ValidationErrorException' ],
        ];
    }
    
    /**
     * @depends testInitOptions
     * @dataProvider providerValidate
     */
    public function testValidate($value, $expected, $options, $exception, $init)
    {
        isset($exception) && $this->setExpectedException(
            (strpos($exception, '\\') === 0 ? "" : "Phpingguo\\System\\Validator\\") . $exception);
    
        $this->assertSame($expected, (new MailAddress())->validate($value, $init($options)));
    }
}
