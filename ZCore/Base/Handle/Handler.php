<?php

namespace Core\Base;

use Core\Base\BaseIOObj;
use Core\Base\Action;

class Handler extends BaseIOObj
{
    /**
     * 建構時會將執行動作放入
     * @var Action
     */
    protected Action $action;

    public function __construct(Action $action)
    {
        parent::__construct();
        $this->action = $action;
    }

    /**
     * 執行動作
     * 
     * @return mixed
     */
    public function run()
    {
        return $this->action->exec();
    }

    /**
     * 嘗試執行動作 忽略錯誤
     * 
     * @return mixed
     */
    public function tryRun()
    {
        try {
            return $this->action->exec();
        } catch (\Throwable $t) {
        }
    }

    /**
     * 當嘗試執行成功時
     */
    public function setSuccessMsg()
    {
        self::getResponse()->render();
    }

    /**
     * 當嘗試執行失敗時
     * @param string $message
     * @param int $errorCode
     */
    public function setFailMsg(string $message, int $errorCode)
    {
        self::getResponse()->setServerMsg('exec', $this->getExecList());
        self::getResponse()->setServerMsg('hint', $message);
        self::getResponse()->setAccess(false);
        self::getResponse()->render($errorCode);
    }
}
