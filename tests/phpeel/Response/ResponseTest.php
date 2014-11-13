<?php
namespace Phpeel\System\Tests\Response;

use Phpeel\ApricotLib\Enums\Charset;
use Phpeel\System\Core\Config;
use Phpeel\System\Enums\ContentType;
use Phpeel\System\Enums\ResponseCode;
use Phpeel\System\Enums\XFrameOptions;
use Phpeel\System\Response\AppApiUrl;
use Phpeel\System\Response\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function providerOutputNormal()
    {
        return [
            [
                [ ResponseCode::OK, ContentType::HTML, Charset::UTF8, XFrameOptions::DENY, 'testだよ！' ],
                [ ResponseCode::OK, ContentType::HTML, Charset::UTF8, XFrameOptions::DENY, 'testだよ！' ]
            ]
        ];
    }

    /**
     * @dataProvider providerOutputNormal
     */
    public function testOutputNormal($actual, $expected)
    {
        $response = new Response();
        
        $response->setResponseCode($actual[0]);
        $response->setContentType($actual[1]);
        $response->setCharset($actual[2]);
        $response->setFrameOption($actual[3]);
        $response->setContent($actual[4]);
        
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isError());
        $this->assertSame($expected[0], $response->getResponseCode()->getValue());
        $this->assertSame($expected[1], $response->getContentType()->getValue());
        $this->assertSame($expected[2], $response->getCharset()->getValue());
        $this->assertSame($expected[3], $response->getFrameOption()->getValue());
        
        $this->expectOutputString($expected[4]);
        $response->output();
    }
    
    public function providerOutputRedirect()
    {
        return [
            [
                true,
                [ ResponseCode::SEE_OTHER, new AppApiUrl() ],
                [ ResponseCode::SEE_OTHER, 'top/index' ]
            ],
            [
                false,
                [ ResponseCode::SEE_OTHER, 'http://hogehoge.com/' ],
                [ ResponseCode::SEE_OTHER, 'http://hogehoge.com/' ]
            ]
        ];
    }

    /**
     * @dataProvider providerOutputRedirect
     */
    public function testOutputRedirect($api_mode, $actual, $expected)
    {
        Config::set('sys.security.allow_redirect_hosts', [ 'hogehoge.com' ]);
        
        $response = new Response();
        
        $this->assertFalse($response->isRedirect());
        $response->setResponseCode($actual[0]);
        ($api_mode === true) && $response->setRedirect($actual[1]);
        ($api_mode === false) && $response->setRedirectUrl($actual[1]);
        $this->assertTrue($response->isRedirect());
        $this->assertSame($expected[0], $response->getResponseCode()->getValue());
        $this->assertSame($expected[1], $response->getRedirectUrl());
        
        $response->output();
    }
    
    public function providerOutputError()
    {
        return [
            [
                [ ResponseCode::INTERNAL_SERVER_ERROR, 'internal server error.' ],
                [ ResponseCode::INTERNAL_SERVER_ERROR, 'internal server error.' ]
            ]
        ];
    }

    /**
     * @dataProvider providerOutputError
     */
    public function testOutputError($actual, $expected)
    {
        $response = new Response();
        
        $this->assertFalse($response->isError());
        $response->ariseError($actual[0], $actual[1]);
        $this->assertTrue($response->isError());
        $this->assertSame($expected[0], $response->getResponseCode()->getValue());
        
        $this->expectOutputString($expected[1]);
        $response->output();
    }

    /**
     * @dataProvider providerOutputError
     */
    public function testOutputErrorWithRedirect($actual, $expected)
    {
        $response = new Response();
        
        $this->assertFalse($response->isRedirect());
        $response->setRedirect(new AppApiUrl());
        $this->assertTrue($response->isRedirect());
        
        $this->assertFalse($response->isError());
        $response->ariseError($actual[0], $actual[1]);
        $this->assertTrue($response->isError());
        $this->assertFalse($response->isRedirect());
        $this->assertSame($expected[0], $response->getResponseCode()->getValue());
        
        $this->expectOutputString($expected[1]);
        $response->output();
    }
}
