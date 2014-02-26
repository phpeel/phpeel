<?php
namespace Phpingguo\Tests\Phpingguo\Validator\String\Latin;

use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\String\Other\FullSizeString;

class ValidatorFStringTest extends \PHPUnit_Framework_TestCase
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
            [ 'a', false, [], 'ValidationErrorException' ],
            [ 'z', false, [], 'ValidationErrorException' ],
            [ 'A', false, [], 'ValidationErrorException' ],
            [ 'Z', false, [], 'ValidationErrorException' ],
            [ 'abc', false, [], 'ValidationErrorException' ],
            [ 'xyz', false, [], 'ValidationErrorException' ],
            [ 'ABC', false, [], 'ValidationErrorException' ],
            [ 'XYZ', false, [], 'ValidationErrorException' ],
            [ '!"#$%&\'()=~|-^\\[]{}/?_*:;+`@,.<>', false, [], 'ValidationErrorException' ],
            [ '1a', false, [], 'ValidationErrorException' ],
            [ 'a1', false, [], 'ValidationErrorException' ],
            [ '1Z', false, [], 'ValidationErrorException' ],
            [ 'Z1', false, [], 'ValidationErrorException' ],
            [ 'ひらがな', true, [], null ],
            [ 'ひらがな1', false, [], 'ValidationErrorException' ],
            [ '1ひらがな', false, [], 'ValidationErrorException' ],
            [ '漢字', true, [], null ],
            [ '漢字1', false, [], 'ValidationErrorException' ],
            [ '1漢字', false, [], 'ValidationErrorException' ],
            [ 'ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'ｶﾀｶﾅ1', false, [], 'ValidationErrorException' ],
            [ '1ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'カタカナ', true, [], null ],
            [ 'カタカナ1', false, [], 'ValidationErrorException' ],
            [ '1カタカナ', false, [], 'ValidationErrorException' ],
            [ 'abcひらがな', false, [], 'ValidationErrorException' ],
            [ 'abc漢字', false, [], 'ValidationErrorException' ],
            [ 'abcｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'abcカタカナ', false, [], 'ValidationErrorException' ],
            [ 'ひらがなabc', false, [], 'ValidationErrorException' ],
            [ 'ひらがな漢字', true, [], null ],
            [ 'ひらがなｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'ひらがなカタカナ', true, [], null ],
            [ '漢字abc', false, [], 'ValidationErrorException' ],
            [ '漢字ひらがな', true, [], null ],
            [ '漢字ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ '漢字カタカナ', true, [], null ],
            [ 'カタカナabc', false, [], 'ValidationErrorException' ],
            [ 'カタカナひらがな', true, [], null ],
            [ 'カタカナ漢字', true, [], null ],
            [ 'カタカナｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'abc', true, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'a b c', true, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'a　b　c', true, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ABC', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'A B C', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'A　B　C', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ひらがな', true, [ 'whitespace' ], null ],
            [ 'ひ ら が な', true, [ 'whitespace' ], null ],
            [ 'ひ　ら　が　な', true, [ 'whitespace' ], null ],
            [ '漢字', true, [ 'whitespace' ], null ],
            [ '漢 字', true, [ 'whitespace' ], null ],
            [ '漢　字', true, [ 'whitespace' ], null ],
            [ 'ｶﾀｶﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ｶ ﾀ ｶ ﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ｶ　ﾀ　ｶ　ﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'カタカナ', true, [ 'whitespace' ], null ],
            [ 'カ タ カ ナ', true, [ 'whitespace' ], null ],
            [ 'カ　タ　カ　ナ', true, [ 'whitespace' ], null ],
            [ 0, false, [ 'nullable' ], 'ValidationErrorException' ],
            [ 0.0, false, [ 'nullable' ], 'ValidationErrorException' ],
            [ '0', false, [ 'nullable' ], 'ValidationErrorException' ],
            [ null, false, [ 'nullable' ], null ],
            [ '', false, [ 'nullable' ], null ],
            [ false, false, [ 'nullable' ], null ],
            [ [], false, [ 'nullable' ], null ],
            [ 0, false, [], 'ValidationErrorException' ],
            [ 0.0, false, [], 'ValidationErrorException' ],
            [ '0', false, [], 'ValidationErrorException' ],
            [ null, false, [], 'ValidationErrorException' ],
            [ '', false, [], 'ValidationErrorException' ],
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
        isset($exception) && $this->setExpectedException("Phpingguo\\System\\Validator\\" . $exception);
        
        $this->assertSame($expected, (new FullSizeString())->validate($value, $init($options)));
    }
}
