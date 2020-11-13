<?php
namespace Core\Base;

class Request
{
    /**
     * 建構時會從 $_SERVER 取得網址
     * @var string
     */
    private string $uri;
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * 建構時會從 $_SERVER 取得訪問方法
     * @var string 
     */
    private string $method;
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * 建構時會透過內建getallheaders() 從 $_SERVER 取得有關 HTTP_ 開頭 header 資訊
     * @var array
     */
    private array $header;
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * 建構時會將從 php://input 取得內容從json轉成php array
     * @var array
     */
    private array $data;
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * 建構時會從 $_SERVER 取得訪問時間
     * @var float
     */
    private float $time;
    public function getTime(): float
    {
        return $this->time;
    }

    function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->header = array_change_key_case(getallheaders(), CASE_LOWER);
        $this->data = json_decode(file_get_contents('php://input'), true) == null ? [] : json_decode(file_get_contents('php://input'), true);
        $this->time = $_SERVER['REQUEST_TIME_FLOAT'];
    }

}
