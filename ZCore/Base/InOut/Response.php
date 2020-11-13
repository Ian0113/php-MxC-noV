<?php
namespace Core\Base;

class Response
{
    /**
     * 回應狀態列表
     * @var array
     */
    private const RESPONSE_STATUS = array(
        100 => 'HTTP/1.1 100 Continue',
        101 => 'HTTP/1.1 101 Switching Protocols',
        200 => 'HTTP/1.1 200 OK',
        201 => 'HTTP/1.1 201 Created',
        202 => 'HTTP/1.1 202 Accepted',
        203 => 'HTTP/1.1 203 Non-Authoritative Information',
        204 => 'HTTP/1.1 204 No Content',
        205 => 'HTTP/1.1 205 Reset Content',
        206 => 'HTTP/1.1 206 Partial Content',
        300 => 'HTTP/1.1 300 Multiple Choices',
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        304 => 'HTTP/1.1 304 Not Modified',
        305 => 'HTTP/1.1 305 Use Proxy',
        307 => 'HTTP/1.1 307 Temporary Redirect',
        400 => 'HTTP/1.1 400 Bad Request',
        401 => 'HTTP/1.1 401 Unauthorized',
        402 => 'HTTP/1.1 402 Payment Required',
        403 => 'HTTP/1.1 403 Forbidden',
        404 => 'HTTP/1.1 404 Not Found',
        405 => 'HTTP/1.1 405 Method Not Allowed',
        406 => 'HTTP/1.1 406 Not Acceptable',
        407 => 'HTTP/1.1 407 Proxy Authentication Required',
        408 => 'HTTP/1.1 408 Request Time-out',
        409 => 'HTTP/1.1 409 Conflict',
        410 => 'HTTP/1.1 410 Gone',
        411 => 'HTTP/1.1 411 Length Required',
        412 => 'HTTP/1.1 412 Precondition Failed',
        413 => 'HTTP/1.1 413 Request Entity Too Large',
        414 => 'HTTP/1.1 414 Request-URI Too Large',
        415 => 'HTTP/1.1 415 Unsupported Media Type',
        416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
        417 => 'HTTP/1.1 417 Expectation Failed',
        500 => 'HTTP/1.1 500 Internal Server Error',
        501 => 'HTTP/1.1 501 Not Implemented',
        502 => 'HTTP/1.1 502 Bad Gateway',
        503 => 'HTTP/1.1 503 Service Unavailable',
        504 => 'HTTP/1.1 504 Gateway Time-out',
        505 => 'HTTP/1.1 505 HTTP Version Not Supported',
    );

    /**
     * 是否已執行過render
     * @var bool
     */
    private static bool $isRender = false;


    /**
     * 資料 會從 setData() 將資料放入
     * @var array
     */
    private ?array $data = null;


    /**
     * 伺服器訊息 會從 setServerMsg() 將訊息放入
     * 
     */
    private ?array $serverMsg = null;


    /**
     * 此次訪問的狀態 會從 setAccess() 放入
     * @var bool
     */
    private bool $access = true;

    /**
     * 回應標頭
     *
     * @param string $var
     * @return void
     */
    public function setHeader(string $var): void
    {
        if (!headers_sent()) {
            header($var);
        }
    }

    /**
     * 回應資料
     *
     * @param string $indexName
     * @param mixed $var
     * @return void
     */
    public function setData(string $indexName, $var): void
    {
        if (!isset($this->data)) $this->data = [];
        $this->data = array_merge($this->data, [
            $indexName => $var,
        ]);
    }

    /**
     * 伺服器提示
     *
     * @param string $indexName
     * @param mixed $var
     * @return void
     */
    public function setServerMsg(string $indexName, $var): void
    {
        if (!isset($this->serverMsg)) $this->serverMsg = [];
        $this->serverMsg = array_merge($this->serverMsg, [
            $indexName => $var,
        ]);
    }

    /**
     * 訪問狀態
     *
     * @return void
     */
    public function setAccess(bool $mod = true): void
    {
        $this->access = $mod;
    }

    /**
     * 將資料渲染成json格式輸出
     *
     * @return void
     */
    public function render(int $errCode = null)
    {
        if (self::$isRender) {
            return;
        }
        if (isset($errCode) && isset(self::RESPONSE_STATUS[$errCode])) {
            $this->setHeader(self::RESPONSE_STATUS[$errCode]);
        }
        $this->setHeader('Content-Type: application/json; charset=UTF-8');

        $dict = [
            'access' => $this->access,
            'data' => $this->data,
        ];
        if (DEBUG) {
            $dict = array_merge($dict, [
                'server' => $this->serverMsg,
            ]);
        }
        echo json_encode($dict);
        self::$isRender = true;
    }
}
