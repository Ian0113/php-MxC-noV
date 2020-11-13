<?php
namespace Core\Base;

use Core\Exceptions\CoreException;

class BaseDBObj
{
    protected static ?\PDO $pdo;

    /**
     * 使用 pdo 連接 db
     */
    protected static function DBConnect()
    {
        try {
            self::$pdo = isset(self::$pdo)? self::$pdo : new \PDO(
                'mysql:charset=utf8mb4;'.
                'host='.DB_HOST.';'.
                'port='.DB_PORT.';'.
                'dbname='.DB_NAME.';',
                DB_USER,
                DB_PASSWORD,
            );
        } catch (\PDOException $pe) {
            throw new CoreException('需要在 /config/db.php 修改DB設定', 500);
        }
    }

    /**
     * 釋放 pdo 資源
     */
    protected static function DBDisconnect()
    {
        self::$pdo = null;
    }

}

