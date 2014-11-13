<?php
namespace Phpeel\System\Enums;

use Phpeel\ApricotLib\Type\Enum\Enum;

/**
 * HTTPレスポンスコードの種類を示します。
 * 
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class ResponseCode extends Enum
{
    // -----------------------------------------------------------------------------
    // 2xx Success Status Code
    // -----------------------------------------------------------------------------
    /** 200 OK - 要求は正常に終了した */
    const OK              = 'HTTP/1.1 200 OK';
    /** 204 No Content - 要求は正常に終了したが、コンテンツが含まれていない */
    const NO_CONTENT      = 'HTTP/1.1 204 No Content';
    /** 205 Reset Content - 要求は正常に終了したが、コンテンツをリセットしなければならない */
    const RESET_CONTENT   = 'HTTP/1.1 205 Reset Content';
    /** 206 Partial Content - 分割されたコンテンツの一部分を処理した */
    const PARTIAL_CONTENT = 'HTTP/1.1 206 Partial Content';

    // -----------------------------------------------------------------------------
    // 3xx Redirection Status Code
    // -----------------------------------------------------------------------------
    /** 301 Moved Permanently - 要求されたリソースは新しい URI に割り当てられた */
    const MOVED_PERMANENTLY  = 'HTTP/1.1 301 Moved Permanently';
    /** 302 Found - 要求されたリソースは一時的に別の URI に割り当てられている */
    const FOUND              = 'HTTP/1.1 302 Found';
    /** 303 See Other - 要求への応答は別の URI にあるリソースを参照する */
    const SEE_OTHER          = 'HTTP/1.1 303 See Other';
    /** 304 Not Modified - 要求されたリソースは更新されていない */
    const NOT_MODIFIED       = 'HTTP/1.1 304 Not Modified';
    /** 305 Use Proxy - 要求されたリソースには、Locationで指定されたプロキシを通してアクセスしなければいけない */
    const USE_PROXY          = 'HTTP/1.1 305 Use Proxy';
    /** 307 Temporary Redirect - 要求されたリソースは、一時的に別の URI へ移動している */
    const TEMPORARY_REDIRECT = 'HTTP/1.1 307 Temporary Redirect';

    // -----------------------------------------------------------------------------
    // 4xx Client Error Status Code
    // -----------------------------------------------------------------------------
    /** 400 Bad Request - 不正なリクエストを行った */
    const BAD_REQUEST              = 'HTTP/1.1 400 Bad Request';
    /** 401 Unauthorized - 要求されたリソースへのアクセスには認証を必要とする */
    const UNAUTHORIZED             = 'HTTP/1.1 401 Unauthorized';
    /** 403 Forbidden - 要求されたリソースへアクセスすることは禁じられている */
    const FORBIDDEN                = 'HTTP/1.1 403 Forbidden';
    /** 404 Not Found - 要求されたリソースは見つからなかった */
    const NOT_FOUND                = 'HTTP/1.1 404 Not Found';
    /** 405 Method Not Allowed - 要求されたリソースに対して許可されていないメソッドを使用した */
    const METHOD_NOT_ALLOWED       = 'HTTP/1.1 405 Method Not Allowed';
    /** 406 Not Acceptable - クライアントはサーバーが受理できない要求を行った */
    const NOT_ACCEPTABLE           = 'HTTP/1.1 406 Not Acceptable';
    /** 408 Request Timeout - 要求待ちで時間切れになった */
    const REQUEST_TIMEOUT          = 'HTTP/1.1 408 Request Timeout';
    /** 411 Length Required - コンテンツの長さが定義されていないため要求を拒否した */
    const LENGTH_REQUIRED          = 'HTTP/1.1 411 Length Required';
    /** 412 Precondition Failed - 前提条件を満たさないものがあったため要求を拒否した */
    const PRECONDITION_FAILED      = 'HTTP/1.1 412 Precondition Failed';
    /** 413 Request Entity Too Large - 要求のエンティティが大きすぎるため要求を拒否した */
    const REQUEST_ENTITY_TOO_LARGE = 'HTTP/1.1 413 Request Entity Too Large';
    /** 414 Request-URI Too Long - 要求した URI が長すぎるため要求を拒否した */
    const REQUEST_URI_TOO_LONG     = 'HTTP/1.1 414 Request-URI Too Long';
    /** 415 Unsupported Media Type - 要求されたリソースでは未サポートのメディアタイプを使用した */
    const UNSUPPORTED_MEDIA_TYPE   = 'HTTP/1.1 415 Unsupported Media Type';

    // -----------------------------------------------------------------------------
    // 5xx Server Error Status Code
    // -----------------------------------------------------------------------------
    /** 500 Internal Server Error - サーバー内部でエラーが発生した */
    const INTERNAL_SERVER_ERROR  = 'HTTP/1.1 500 Internal Server Error';
    /** 501 Not Implemented - 実装されていないメソッドを使用した */
    const NOT_IMPLEMENTED        = 'HTTP/1.1 501 Not Implemented';
    /** 503 Service Unavailable - サービスは一時的に利用不可能な状況である */
    const SERVICE_UNAVAILABLE    = 'HTTP/1.1 503 Service Unavailable';
    /** 505 HTTP Version Not Supported - 未サポートのHTTPバージョンで要求した */
    const HTTP_VER_NOT_SUPPORTED = 'HTTP/1.1 505 HTTP Version Not Supported';
}
