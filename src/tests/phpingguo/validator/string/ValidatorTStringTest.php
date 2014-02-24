<?php
use Phpingguo\System\Validator\String\Other\TextString;
use Phpingguo\System\Validator\Options;

class ValidatorTStringTest extends PHPUnit_Framework_TestCase
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
            [ 'A', true, [], null ],
            [ 'Z', true, [], null ],
            [ 'abc', true, [], null ],
            [ 'xyz', true, [], null ],
            [ 'ABC', true, [], null ],
            [ 'XYZ', true, [], null ],
            [ '!"#$%&\'()=~|-^\\[]{}/?_*:;+`@,.<>', true, [], null ],
            [ '1a', true, [], null ],
            [ 'a1', true, [], null ],
            [ '1Z', true, [], null ],
            [ 'Z1', true, [], null ],
            [ 'ひらがな', true, [], null ],
            [ 'ひらがな1', true, [], null ],
            [ '1ひらがな', true, [], null ],
            [ '漢字', true, [], null ],
            [ '漢字1', true, [], null ],
            [ '1漢字', true, [], null ],
            [ 'ｶﾀｶﾅ', true, [], null ],
            [ 'ｶﾀｶﾅ1', true, [], null ],
            [ '1ｶﾀｶﾅ', true, [], null ],
            [ 'カタカナ', true, [], null ],
            [ 'カタカナ1', true, [], null ],
            [ '1カタカナ', true, [], null ],
            [ 'abcひらがな', true, [], null ],
            [ 'abc漢字', true, [], null ],
            [ 'abcｶﾀｶﾅ', true, [], null ],
            [ 'abcカタカナ', true, [], null ],
            [ 'ひらがなabc', true, [], null ],
            [ 'ひらがな漢字', true, [], null ],
            [ 'ひらがなｶﾀｶﾅ', true, [], null ],
            [ 'ひらがなカタカナ', true, [], null ],
            [ '漢字abc', true, [], null ],
            [ '漢字ひらがな', true, [], null ],
            [ '漢字ｶﾀｶﾅ', true, [], null ],
            [ '漢字カタカナ', true, [], null ],
            [ 'カタカナabc', true, [], null ],
            [ 'カタカナひらがな', true, [], null ],
            [ 'カタカナ漢字', true, [], null ],
            [ 'カタカナｶﾀｶﾅ', true, [], null ],
            [ 'abc', true, [ 'whitespace' ], null ],
            [ 'a b c', true, [ 'whitespace' ], null ],
            [ 'a　b　c', true, [ 'whitespace' ], null ],
            [ 'ABC', true, [ 'whitespace' ], null ],
            [ 'A B C', true, [ 'whitespace' ], null ],
            [ 'A　B　C', true, [ 'whitespace' ], null ],
            [ 'ひらがな', true, [ 'whitespace' ], null ],
            [ 'ひ ら が な', true, [ 'whitespace' ], null ],
            [ 'ひ　ら　が　な', true, [ 'whitespace' ], null ],
            [ '漢字', true, [ 'whitespace' ], null ],
            [ '漢 字', true, [ 'whitespace' ], null ],
            [ '漢　字', true, [ 'whitespace' ], null ],
            [ 'ｶﾀｶﾅ', true, [ 'whitespace' ], null ],
            [ 'ｶ ﾀ ｶ ﾅ', true, [ 'whitespace' ], null ],
            [ 'ｶ　ﾀ　ｶ　ﾅ', true, [ 'whitespace' ], null ],
            [ 'カタカナ', true, [ 'whitespace' ], null ],
            [ 'カ タ カ ナ', true, [ 'whitespace' ], null ],
            [ 'カ　タ　カ　ナ', true, [ 'whitespace' ], null ],
            [ 0, false, [ 'nullable' ], 'ValidationErrorException' ],
            [ 0.0, false, [ 'nullable' ], 'ValidationErrorException' ],
            [ '0', true, [ 'nullable' ], null ],
            [ null, false, [ 'nullable' ], null ],
            [ '', false, [ 'nullable' ], null ],
            [ false, false, [ 'nullable' ], null ],
            [ [], false, [ 'nullable' ], null ],
            [ 0, false, [], 'ValidationErrorException' ],
            [ 0.0, false, [], 'ValidationErrorException' ],
            [ '0', true, [], null ],
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
        isset($exception) && $this->setExpectedException("Phpingguo\System\Exceptions\\" . $exception);
        
        $this->assertSame($expected, (new TextString())->validate($value, $init($options)));
    }
}
