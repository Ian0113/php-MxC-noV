<?php
namespace Core\Base;

use Core\Exceptions\SessionException;

// ref. https://www.php.net/manual/en/reserved.variables.session.php
class Session
{
    /**
     * 在定義的Session中取得一個變數
     * 會在 $_SESSION 內尋找並使 $var = $_SESSION[$sessionName][$indexName];
     * 取得成功回傳true 反之false
     * 
     * @param string $sessionName
     * @param string $indexName
     * @param mixed  &$var
     * 
     * @return bool
     */
    public static function get(string $sessionName, string $indexName, &$var): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[$sessionName])) {
            if (isset($_SESSION[$sessionName][$indexName])) {
                $var = $_SESSION[$sessionName][$indexName];
                return true;
            }
        }
        return false;
    }

    /**
     * 在定義的Session中更新一個變數
     * 會在 $_SESSION 內尋找並使 $_SESSION[$sessionName][$indexName] = $var;
     * 更新成功回傳true 反之false
     * 
     * @param string $sessionName
     * @param string $indexName
     * @param mixed  $var
     * 
     * @return bool
     */
    public static function update(string $sessionName, string $indexName, $var): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[$sessionName])) {
            if (isset($_SESSION[$sessionName][$indexName])) {
                $_SESSION[$sessionName][$indexName] = $var;
                return true;
            }
        }
        return false;
    }

    /**
     * 在定義的Session中刪除一個變數
     * 會在 $_SESSION 內尋找並使 unset $_SESSION[$sessionName][$indexName];
     * 刪除成功回傳true 反之false
     * 
     * @param string $sessionName
     * @param string $indexName
     * 
     * @return bool
     */
    public static function delete(string $sessionName, string $indexName)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[$sessionName])) {
            if (isset($_SESSION[$sessionName][$indexName])) {
                unset($_SESSION[$sessionName][$indexName]);
                return true;
            }
        }
        return false;
    }

    /**
     * 在定義的Session中增加一個變數
     * 會在 $_SESSION 內新增 $_SESSION[$sessionName][$indexName] = $var;
     * 新增成功回傳true 反之false
     * 
     * @param string $sessionName
     * @param string $indexName
     * @param mixed  $var
     * 
     * @return bool
     */
    public static function set(string $sessionName, string $indexName, $var): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[$sessionName])) {
            if (!isset($_SESSION[$sessionName][$indexName])) {
                $_SESSION[$sessionName][$indexName] = $var;
                return true;
            }
        }
        return false;
    }

    /**
     * 用來定義新的Session
     * 會在 $_SESSION 內新增 $SESSION[$sessionName]
     * 新增成功回傳true 反之false
     * 
     * @param string $sessionName
     * 
     * @return bool
     */
    public static function register(string $sessionName): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[$sessionName])) {
            $_SESSION[$sessionName] = [];
            // var_dump($_SESSION[$sessionName]);
            return true;
        }
        // var_dump($_SESSION[$sessionName]);
        return false;
    }

    /**
     * 用來取消特定Session
     * 會在 $_SESSION 內找到 $SESSION[$sessionName] 並unset
     * 取消成功回傳true 反之false
     * 
     * @param string $sessionName
     * 
     * @return bool
     */
    public static function unregister(string $sessionName): bool
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION[$sessionName])) {
            unset($_SESSION[$sessionName]);
            // var_dump($_SESSION);
            return true;
        }
        return false;
    }
}
