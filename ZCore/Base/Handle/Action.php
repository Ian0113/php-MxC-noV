<?php

namespace Core\Base;

class Action
{
    /**
     * 建構時放入 類別實例
     * @var string
     */
    private string $class;

    /**
     * 建構時放入 方法名稱
     * @var string
     */
    private string $function;

    /**
     * 建構時放入 方法參數
     * @var array
     */
    private array $args;

    public function __construct(string $class, string $function, array $args = [])
    {
        $this->class = $class;
        $this->function = $function;
        $this->args = $args;
    }

    /**
     * 執行class中的function
     * 
     * @return mixed
     */
    public function exec()
    {
        // 取得方法參數 ref. https://www.php.net/manual/en/reflectionfunctionabstract.getparameters.php
        $reflection = new \ReflectionMethod($this->class, $this->function);
        $tmpArgs = []; // 排序後的結果
        foreach ($reflection->getParameters() as $parameter) {
            // 檢查function是否有這參數
            if (isset($this->args[$parameter->name])) {
                $tmpArgs = array_merge($tmpArgs, [$parameter->name => $this->args[$parameter->name]]);
            }
        }
        // 執行 ref. https://www.php.net/manual/en/function.call-user-func-array.php
        return call_user_func_array(array(new $this->class, $this->function), $tmpArgs);
    }
}
