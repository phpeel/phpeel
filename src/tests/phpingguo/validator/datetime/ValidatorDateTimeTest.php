<?php
use Phpingguo\System\Validator\DateTime\DateTime;
use Phpingguo\System\Validator\Options;

class ValidatorDateTimeTest extends PHPUnit_Framework_TestCase
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
            [ '2000-01-01 00:00:00', true, [], null ],
            [ '2000-01-01 00:00', true, [], null ],
            [ '2000-01-01 000000', true, [], null ],
            [ '2000-01-01 0000', true, [], null ],
            [ '2000/01/01 00:00:00', true, [], null ],
            [ '2000/01/01 00:00', true, [], null ],
            [ '2000/01/01 000000', true, [], null ],
            [ '2000/01/01 0000', true, [], null ],
            [ '20000101 00:00:00', true, [], null ],
            [ '20000101 00:00', true, [], null ],
            [ '20000101 000000', true, [], null ],
            [ '20000101 0000', true, [], null ],
            [ '2000-01-01', false, [], 'ValidationErrorException' ],
            [ '2000/01/01', false, [], 'ValidationErrorException' ],
            [ '20000101', false, [], 'ValidationErrorException' ],
            [ '00:00:00', false, [], 'ValidationErrorException' ],
            [ '00:00', false, [], 'ValidationErrorException' ],
            [ '000000', false, [], 'ValidationErrorException' ],
            [ '0000', false, [], 'ValidationErrorException' ],
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
        isset($exception) && $this->setExpectedException("Phpingguo\System\Exceptions\\" . $exception);
        
        $this->assertSame($expected, (new DateTime())->validate($value, $init($options)));
    }
}
