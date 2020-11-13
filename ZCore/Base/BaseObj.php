<?php

namespace Core\Base;

use Core\Base\Request;
use Core\Base\Response;

class BaseObj
{

    /**
     * 紀錄執行路徑
     * @var array
     */
    private static array $execList = [];
    private static array $nowExec;
    protected array $preExec;
    public static function getExecList()
    {
        return self::$execList;
    }

    public function __construct()
    {
        // 判斷是不是初次進入
        if (count(self::$execList) == 0) {
            self::$nowExec = &self::$execList;
        }

        // 將執行路徑新增至上層執行列表
        self::$nowExec = array_merge(self::$nowExec, [[
            'class' => get_class($this),
            'is_completed' => false,
            'exec' => [],
        ]]);

        // 將指標指到現在執行列表
        for ($i=0; $i < count(self::$nowExec); $i++) { 
            if (self::$nowExec[$i]['class'] == get_class($this) && !self::$nowExec[$i]['is_completed']) {
                $this->preExec = &self::$nowExec;
                self::$nowExec = &self::$nowExec[$i]['exec'];
                break;
            }
        }
    }

    public function __destruct()
    {
        // 更新執行狀態
        for ($i=0; $i < count($this->preExec); $i++) { 
            if ($this->preExec[$i]['class'] == get_class($this) && !$this->preExec[$i]['is_completed']) {
                $this->preExec[$i]['is_completed'] = true;
                if (count($this->preExec[$i]['exec']) == 0) {
                    unset($this->preExec[$i]['exec']);
                }
                break;
            }
        }
        // 將指標指回上個路徑
        self::$nowExec = &$this->preExec;
    }

}
