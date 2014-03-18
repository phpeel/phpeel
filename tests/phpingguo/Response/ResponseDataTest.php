<?php
namespace Phpingguo\System\Tests\Response;

use Phpingguo\ApricotLib\Enums\Charset;
use Phpingguo\System\Core\Config;
use Phpingguo\System\Enums\ContentType;
use Phpingguo\System\Enums\ResponseCode;
use Phpingguo\System\Enums\XFrameOptions;
use Phpingguo\System\Response\AppApiUrl;
use Phpingguo\System\Response\ResponseData;

class ResponseDataTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultInitialize()
    {
        $data = new ResponseData();
        
        $this->assertSame(ResponseCode::OK, $data->getResponseCode()->getValue());
        $this->assertSame(ContentType::HTML, $data->getContentType()->getValue());
        $this->assertSame(Charset::UTF8, $data->getCharset()->getValue());
        $this->assertSame(XFrameOptions::DENY, $data->getFrameOption()->getValue());
        $this->assertNull($data->getContent());
    }
    
    public function testDefaultExceptions()
    {
        $this->setExpectedException('RuntimeException');
        
        $data = new ResponseData();
        $data->getRedirectUrl();
    }

    public function providerPropertiesSuccess()
    {
        return [
            [
                [ 'code' => ResponseCode::INTERNAL_SERVER_ERROR ],
                [ ResponseCode::INTERNAL_SERVER_ERROR, ContentType::HTML, Charset::UTF8, XFrameOptions::DENY, null ]
            ],
            [
                [ 'type' => ContentType::JSON ],
                [ ResponseCode::OK, ContentType::JSON, Charset::UTF8, XFrameOptions::DENY, null ]
            ],
            [
                [ 'charset' => Charset::SJIS ],
                [ ResponseCode::OK, ContentType::HTML, Charset::SJIS, XFrameOptions::DENY, null ]
            ],
            [
                [ 'frame' => XFrameOptions::SAME_ORIGIN ],
                [ ResponseCode::OK, ContentType::HTML, Charset::UTF8, XFrameOptions::SAME_ORIGIN, null ]
            ],
            [
                [ 'content' => 'hogehoge' ],
                [ ResponseCode::OK, ContentType::HTML, Charset::UTF8, XFrameOptions::DENY, 'hogehoge' ]
            ]
        ];
    }
    
    /**
     * @dataProvider providerPropertiesSuccess
     */
    public function testPropertiesSuccess($actual, $expected)
    {
        $data = new ResponseData();
        
        array_key_exists('code', $actual) && $data->setResponseCode($actual['code']);
        array_key_exists('type', $actual) && $data->setContentType($actual['type']);
        array_key_exists('charset', $actual) && $data->setCharset($actual['charset']);
        array_key_exists('frame', $actual) && $data->setFrameOption($actual['frame']);
        array_key_exists('content', $actual) && $data->setContent($actual['content']);
        
        $this->assertSame($expected[0], $data->getResponseCode()->getValue());
        $this->assertSame($expected[1], $data->getContentType()->getValue());
        $this->assertSame($expected[2], $data->getCharset()->getValue());
        $this->assertSame($expected[3], $data->getFrameOption()->getValue());
        $this->assertSame($expected[4], $data->getContent());
    }
    
    public function providerPropertiesFailed()
    {
        return [
            [ null, null ],
            [ '', 'InvalidArgumentException' ],
            [ true, 'InvalidArgumentException' ],
            [ false, 'InvalidArgumentException' ],
            [ [], 'InvalidArgumentException' ],
            [ [ 'a' ], 'InvalidArgumentException' ],
            [ new \stdClass(), 'InvalidArgumentException' ],
            [ 0, 'InvalidArgumentException' ],
            [ 0.0, 'InvalidArgumentException' ],
            [ 0.1, 'InvalidArgumentException' ],
            [ '0', 'InvalidArgumentException' ],
            [ '0.0', 'InvalidArgumentException' ],
            [ '0.1', 'InvalidArgumentException' ],
        ];
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetResponseCodeFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ResponseData();
        $data->setResponseCode($actual);
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetContentTypeFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ResponseData();
        $data->setContentType($actual);
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetCharsetFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ResponseData();
        $data->setCharset($actual);
    }

    /**
     * @dataProvider providerPropertiesFailed
     */
    public function testSetFrameOptionFailed($actual, $exception)
    {
        isset($exception) && $this->setExpectedException($exception);
        
        $data = new ResponseData();
        $data->setFrameOption($actual);
    }
    
    public function providerSetContent()
    {
        return [
            [ null, null ],
            [ '', null ],
            [ true, null ],
            [ false, null ],
            [ [], null ],
            [ [ 'a' ], null ],
            [ new \stdClass(), null ],
            [ 0, null ],
            [ 0.0, null ],
            [ 0.1, null ],
            [ '0', '0' ],
            [ '0.0', '0.0' ],
            [ '0.1', '0.1' ],
        ];
    }

    /**
     * @dataProvider providerSetContent
     */
    public function testSetContent($actual, $expected)
    {
        $data = new ResponseData();
        
        $data->setContent($actual);
        $this->assertSame($expected, $data->getContent());
    }
    
    public function testRedirectApiSuccess()
    {
        $data = new ResponseData();
        
        $this->assertFalse($data->isRedirect());
        $data->setRedirect(new AppApiUrl());
        $this->assertTrue($data->isRedirect());
        $this->assertSame('top/index', $data->getRedirectUrl());
    }
    
    public function testRedirectApiFailed()
    {
        $this->setExpectedException('RuntimeException');
        
        $data = new ResponseData();
        
        $data->setRedirect(new AppApiUrl());
        $data->setRedirect(new AppApiUrl());
    }
    
    public function testRedirectUrlSuccess()
    {
        Config::set('sys.security.allow_redirect_hosts', [ 'hogehoge.com' ]);
        
        $data = new ResponseData();
        
        $this->assertFalse($data->isRedirect());
        $data->setRedirectUrl('http://hogehoge.com/');
        $this->assertTrue($data->isRedirect());
        $this->assertSame('http://hogehoge.com/', $data->getRedirectUrl());
    }
    
    public function providerRedirectUrlFailed()
    {
        return [
            [ 'http://hogehoge2.com/', 'RuntimeException' ],
            [ null, 'InvalidArgumentException' ],
            [ '', 'InvalidArgumentException' ],
            [ true, 'InvalidArgumentException' ],
            [ false, 'InvalidArgumentException' ],
            [ [], 'InvalidArgumentException' ],
            [ [ 'a' ], 'InvalidArgumentException' ],
            [ new \stdClass(), 'InvalidArgumentException' ],
            [ 0, 'InvalidArgumentException' ],
            [ 0.0, 'InvalidArgumentException' ],
            [ 0.1, 'InvalidArgumentException' ],
            [ '0', 'InvalidArgumentException' ],
            [ '0.0', 'InvalidArgumentException' ],
            [ '0.1', 'InvalidArgumentException' ],
        ];
    }

    /**
     * @dataProvider providerRedirectUrlFailed
     */
    public function testRedirectUrlFailed($url, $exception)
    {
        $this->setExpectedException($exception);
        Config::set('sys.security.allow_redirect_hosts', [ 'hogehoge.com' ]);
        
        $data = new ResponseData();
        
        $this->assertFalse($data->isRedirect());
        $data->setRedirectUrl($url);
    }
    
    public function providerAriseError()
    {
        return [
            [ ResponseCode::INTERNAL_SERVER_ERROR, 'internal server error.' ],
            [ ResponseCode::BAD_REQUEST, 'bad request.' ],
            [ ResponseCode::FORBIDDEN, 'forbidden.' ],
            [ ResponseCode::NOT_FOUND, 'not found.' ],
            [ ResponseCode::UNAUTHORIZED, 'unauthorized.' ],
        ];
    }

    /**
     * @dataProvider providerAriseError
     */
    public function testAriseErrorNormal($code, $message)
    {
        $data = new ResponseData();
        
        $this->assertFalse($data->isError());
        $data->ariseError($code, $message);
        $this->assertTrue($data->isError());
        $this->assertSame($code, $data->getResponseCode()->getValue());
        $this->assertSame($message, $data->getContent());
    }

    /**
     * @dataProvider providerAriseError
     */
    public function testAriseErrorWithRedirect($code, $message)
    {
        $data = new ResponseData();
        
        $this->assertFalse($data->isRedirect());
        $data->setRedirect(new AppApiUrl());
        $this->assertTrue($data->isRedirect());
        
        $this->assertFalse($data->isError());
        $data->ariseError($code, $message);
        $this->assertTrue($data->isError());
        $this->assertFalse($data->isRedirect());
        
        $this->assertSame($code, $data->getResponseCode()->getValue());
        $this->assertSame($message, $data->getContent());
    }
}
