<?php
namespace Core\Base;

use Core\Base\BaseDBObj;

class Model extends BaseDBObj
{
    protected string $table;

    public function __construct()
    {
        self::DBConnect();
    }

    function destruct()
    {
        self::DBDisconnect();
    }

    /**
     * 執行 sql 語法
     */
    protected static function SQLQuery(string $sql, array $pre)
    {
        try {
            $query = self::$pdo->prepare($sql);
            $query->execute($pre);
            return $query;
        } catch (\PDOException $pe) {
            throw new CoreException('SQL 語法錯誤', 500);
        }
    }


    /**
     * 製作 where sql 語法
     * 
     * @param array $wp = [[field, op, value], [field, op, value], ......]
     */
    protected static function where(array &$pre, array $wp)
    {
        $whs = array();
        foreach ($wp as $arr) {
            $pre[':w_'.$arr[0]] = $arr[2];
            $whs[':w_'.$arr[0]] = "{$arr[0]} {$arr[1]} :w_{$arr[0]}";
        }
        return count($wp) == 0 ? '' : ' WHERE BINARY '.implode(', ', array_values($whs));
    }


    /**
     * 製作 select sql 語法
     * 
     * @param array $wp = [[field, op, value], [field, op, value], ......]
     */
    public function selectAll(array $wp = [])
    {
        $pre = array();
        $str_pre_where = self::where($pre, $wp);
        $sql = "SELECT * FROM `{$this->table}` {$str_pre_where} ;";

        return self::SQLQuery($sql, $pre)->fetchAll();
    }


    /**
     * 製作 select sql 語法
     * 
     * @param array $fields = [field, field, ......]
     * @param array $wp = [[field, op, value], [field, op, value], ......]
     */
    public function select(array $fields, array $wp = [])
    {
        $pre = array();
        $str_pre_where = self::where($pre, $wp);

        $str_fields = implode(', ', $fields);
        $sql = "SELECT ({$str_fields}) FROM `{$this->table}` {$str_pre_where} ;";
        return self::SQLQuery($sql, $pre)->fetchAll();
    }


    /**
     * 製作 insert sql 語法
     * 
     * @param array $dataKV = [field => value, field => value, ......]
     */
    public function insert(array $dataKV)
    {
        $pre = array();
        foreach ($dataKV as $key => $value) {
            $pre[':'.$key] = $value;
        }
        $str_fields = implode(', ', array_keys($dataKV));
        $str_pre_fields = implode(', ', array_keys($pre));

        $sql = "INSERT INTO `{$this->table}` ({$str_fields}) VALUES ({$str_pre_fields}) ;";
        return self::SQLQuery($sql, $pre);
    }

    /**
     * 製作 update sql 語法
     * 
     * @param array $dataKV = [field => value, field => value,......]
     * @param array $wp = [[field, op, value], [field, op, value], ......]
     */
    public function update(array $dataKV, array $wp = [])
    {
        $pre = array();
        $uds = array();
        foreach ($dataKV as $key => $value) {
            $uds[':'.$key] = "$key=:$key";
            $pre[':'.$key] = $value;
        }
        $str_pre_set = implode(', ', array_values($uds));

        $str_pre_where = self::where($pre, $wp);

        $sql = "UPDATE {$this->table} SET {$str_pre_set} {$str_pre_where} ;";
        return self::SQLQuery($sql, $pre);
    }


    /**
     * 製作 delete sql 語法
     * 
     * @param array $wp = [[field, op, value], [field, op, value], ......]
     */
    public function delete(array $wp = [])
    {
        $pre = array();
        $str_pre_where = self::where($pre, $wp);
        $sql = "DELETE FROM `{$this->table}` {$str_pre_where} ;";
        return self::SQLQuery($sql, $pre);
    }

}
