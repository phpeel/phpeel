<?php
namespace Phpingguo\Tests\Phpingguo\Validator\String\Latin;

use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\String\Latin\LowerAlphabet;

class ValidatorLAlphabetTest extends \PHPUnit_Framework_TestCase
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
            [ 'a', true, [], null ],
            [ 'z', true, [], null ],
            [ 'A', false, [], 'ValidationErrorException' ],
            [ 'Z', false, [], 'ValidationErrorException' ],
            [ 'abc', true, [], null ],
            [ 'xyz', true, [], null ],
            [ 'ABC', false, [], 'ValidationErrorException' ],
            [ 'XYZ', false, [], 'ValidationErrorException' ],
            [ '!"#$%&\'()=~|-^\\[]{}/?_*:;+`@,.<>', false, [], 'ValidationErrorException' ],
            [ '1a', false, [], 'ValidationErrorException' ],
            [ 'a1', false, [], 'ValidationErrorException' ],
            [ '1Z', false, [], 'ValidationErrorException' ],
            [ 'Z1', false, [], 'ValidationErrorException' ],
            [ 'ひらがな', false, [], 'ValidationErrorException' ],
            [ 'ひらがな1', false, [], 'ValidationErrorException' ],
            [ '1ひらがな', false, [], 'ValidationErrorException' ],
            [ '漢字', false, [], 'ValidationErrorException' ],
            [ '漢字1', false, [], 'ValidationErrorException' ],
            [ '1漢字', false, [], 'ValidationErrorException' ],
            [ 'ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'ｶﾀｶﾅ1', false, [], 'ValidationErrorException' ],
            [ '1ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'カタカナ', false, [], 'ValidationErrorException' ],
            [ 'カタカナ1', false, [], 'ValidationErrorException' ],
            [ '1カタカナ', false, [], 'ValidationErrorException' ],
            [ 'abcひらがな', false, [], 'ValidationErrorException' ],
            [ 'abc漢字', false, [], 'ValidationErrorException' ],
            [ 'abcｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'abcカタカナ', false, [], 'ValidationErrorException' ],
            [ 'ひらがなabc', false, [], 'ValidationErrorException' ],
            [ 'ひらがな漢字', false, [], 'ValidationErrorException' ],
            [ 'ひらがなｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'ひらがなカタカナ', false, [], 'ValidationErrorException' ],
            [ '漢字abc', false, [], 'ValidationErrorException' ],
            [ '漢字ひらがな', false, [], 'ValidationErrorException' ],
            [ '漢字ｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ '漢字カタカナ', false, [], 'ValidationErrorException' ],
            [ 'カタカナabc', false, [], 'ValidationErrorException' ],
            [ 'カタカナひらがな', false, [], 'ValidationErrorException' ],
            [ 'カタカナ漢字', false, [], 'ValidationErrorException' ],
            [ 'カタカナｶﾀｶﾅ', false, [], 'ValidationErrorException' ],
            [ 'abc', true, [ 'whitespace' ], null ],
            [ 'a b c', true, [ 'whitespace' ], null ],
            [ 'a　b　c', true, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ABC', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'A B C', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'A　B　C', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ひらがな', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ひ ら が な', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ひ　ら　が　な', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ '漢字', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ '漢 字', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ '漢　字', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ｶﾀｶﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ｶ ﾀ ｶ ﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'ｶ　ﾀ　ｶ　ﾅ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'カタカナ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'カ タ カ ナ', false, [ 'whitespace' ], 'ValidationErrorException' ],
            [ 'カ　タ　カ　ナ', false, [ 'whitespace' ], 'ValidationErrorException' ],
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
        
        $this->assertSame($expected, (new LowerAlphabet())->validate($value, $init($options)));
    }
}
