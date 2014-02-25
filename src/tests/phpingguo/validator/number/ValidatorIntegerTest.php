<?php
use Phpingguo\System\Validator\Number\Integer;
use Phpingguo\System\Validator\Options;

class ValidatorIntegerTest extends PHPUnit_Framework_TestCase
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
            [ 1, true, [], null ],
            [ 100, true, [], null ],
            [ PHP_INT_MAX - 1, true, [], null ],
            [ PHP_INT_MAX, true, [], null ],
            [ PHP_INT_MAX + 1, false, [], 'ValidationErrorException' ],
            [ PHP_INT_MAX + 10, false, [], 'ValidationErrorException' ],
            [ -1, true, [], null ],
            [ -100, true, [], null ],
            [ ~PHP_INT_MAX + 1, true, [], null ],
            [ ~PHP_INT_MAX, true, [], null ],
            [ ~PHP_INT_MAX - 1, false, [], 'ValidationErrorException' ],
            [ ~PHP_INT_MAX - 10, false, [], 'ValidationErrorException' ],
            [ -100, false, [ 'min' => -50, 'max' => 50 ], 'ValidationErrorException' ],
            [ -51, false, [ 'min' => -50, 'max' => 50 ], 'ValidationErrorException' ],
            [ -50, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ -49, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ -1, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ 0, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ 1, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ 49, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ 50, true, [ 'min' => -50, 'max' => 50 ], null ],
            [ 51, false, [ 'min' => -50, 'max' => 50 ], 'ValidationErrorException' ],
            [ 100, false, [ 'min' => -50, 'max' => 50 ], 'ValidationErrorException' ],
            [ 0, true, [ 'nullable' ], null ],
            [ 0.0, true, [ 'nullable' ], null ],
            [ '0', true, [ 'nullable' ], null ],
            [ null, false, [ 'nullable' ], null ],
            [ '', false, [ 'nullable' ], null ],
            [ false, false, [ 'nullable' ], null ],
            [ [], false, [ 'nullable' ], null ],
            [ 0, true, [], null ],
            [ 0.0, true, [], null ],
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
        isset($exception) && $this->setExpectedException("Phpingguo\\System\\Validator\\" . $exception);
        
        $this->assertSame($expected, (new Integer())->validate($value, $init($options)));
    }
}
