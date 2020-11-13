<?php
namespace Core\Base;

use Core\Base\Session;
use Core\Base\BaseIOObj;
use Core\Exceptions\MiddlewareException;


class Middleware extends BaseIOObj
{
    /**
     * 建構時會將 類別名稱 設為 sessionName
     * 會把下列 function 內操作變數設為 $_SESSION[$sessionName]
     * 
     * $this->deleteSession()
     * $this->setSession()
     * $this->getSession()
     * $this->updateSession()
     * 
     * @var string
     */
    protected string $sessionName;

    /**
     * 每分鐘的訪問次數
     *
     * @var int
     */
    protected int $timesPerMin = 0;

    /**
     * 驗證 self::getResponse()->getHeader()
     * 驗證方法 self::isHeaderVerified()
     * 
     * @var array
     */
    protected array $header = [
    ];


    /**
     * 驗證 self::getResponse()->getData()
     * 驗證方法 self::isDataVerified()
     * 
     * @var array
     */
    protected array $data = [
    ];


    public function __construct()
    {
        parent::__construct();

        // 物件記錄用Session
        $this->sessionName = get_class($this);
        Session::register($this->sessionName);
    }

    /**
     * Core 自動執行區域
     */
    protected function run()
    {
        if (!self::isDataVerified() || !self::isHeaderVerified()) {
            throw new MiddlewareException("header || data", 401);
        }

        if (!$this->isTimesPerMinVerified()) {
            throw new MiddlewareException("訪問次數過多", 403);
        }
    }

    /**
     * 驗證 $arr 內是否有 $chkArr 的 index 或 index / value
     * 說明 :
     * 'aaa' => null 為驗證 isset($arr['aaa'])
     * 'aaa' => 123  為驗證 isset($arr['aaa']) && $arr['aaa'] == 123
     * 
     * @param array $arr
     * @param array $chkArr
     * @return bool
     */
    protected static function isArraySameVerified(array $arr, array $chkArr): bool
    {
        foreach ($chkArr as $key => $value) {
            if (!isset($arr[$key]) || ($value != null && $arr[$key] != $value)) return false;
        }
        return true;
    }

    /**
     * 設定 Session
     * $autoUpdate = true 設定不成即更新
     * 
     * @param string $index
     * @param mixed $var
     * @param bool $autoUpdate
     * @return bool
     */
    protected function setSession(string $index, $var, bool $autoUpdate = false): bool
    {
        if ($autoUpdate) {
            return Session::set($this->sessionName, $index, $var) || Session::update($this->sessionName, $index, $var);
        }
        return Session::set($this->sessionName, $index, $var);
    }

    /**
     * 更新 Session
     * 
     * @param string $index
     * @param mixed $var
     * @return bool
     */
    protected function updateSession(string $index, $var): bool
    {
        return Session::update($this->sessionName, $index, $var);
    }


    /**
     * 取得 Session
     * 
     * @param string $index
     * @return bool
     */
    protected function getSession(string $index, &$results): bool
    {
        return Session::get($this->sessionName, $index, $results);
    }

    /**
     * 刪除 Session
     * 
     * @param string $index
     * @return bool
     */
    protected function deleteSession(string $index): bool
    {
        return Session::delete($this->sessionName, $index);
    }


        /**
     * 驗證 header 內是否有符合列表內所有項目
     * 與 $this->header 相關
     * 
     * @return bool
     */
    protected function isHeaderVerified(): bool
    {
        return self::isArraySameVerified(self::getRequest()->getHeader(), $this->header);
    }


    /**
     * 驗證 data 內是否有符合列表內所有項目
     * 與 $this->data 相關
     * 
     * @return bool
     */
    protected function isDataVerified(): bool
    {
        return self::isArraySameVerified(self::getRequest()->getData(), $this->data);
    }

    /**
     * 設定成可跨域訪問
     *
     * @param string $url
     * @return void
     */
    protected static function setRequestAllow(string $url = '*', string $headers = '*', string $method = '*')
    {
        self::getResponse()->setHeader('Access-Control-Allow-Credentials:true');
        self::getResponse()->setHeader("Access-Control-Allow-Origin:{$url}");
        self::getResponse()->setHeader("Access-Control-Allow-Headers:{$headers}");
        self::getResponse()->setHeader("Access-Control-Allow-Methods:{$method}");
    }

        /**
     * 檢查 Request 訪問次數
     * 與 $this->timesPerMin 相關
     * 
     * @return bool
     */
    private function isTimesPerMinVerified()
    {
        if ($this->timesPerMin <= 0 || $this->timesPerMin == null) {
            // 不限次數
            return true;
        }

        $sessionIndexName = 'isTimesPerMinVerified';
        $time = self::getRequest()->getTime();
        $chk = $this->getSession($sessionIndexName, $results);
        if (!$chk || $time > $results['time']) {
            // 初次設定 || 過 60s
            $this->setSession($sessionIndexName, [
                'time'  => $time + 60,
                'cnt'   => 0,
            ], true);
            return true;
        }

        if ($this->timesPerMin > $results['cnt']) {
            // 時間區間內訪問 紀錄次數
            $results['cnt'] += 1;
            $this->updateSession($sessionIndexName, $results);
            return true;
        }
        return false;
    }

}
