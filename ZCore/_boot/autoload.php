<?php

function findAppPath($cname, $path)
{
    $filePathList = glob($path.'/*');
    if (count($filePathList) == 0) return false;
    $isFind = false;
    foreach ($filePathList as $filePath) {
        if (is_dir($filePath)) {
            $isFind = findAppPath($cname, $filePath);
            continue;
        }

        if (is_file($filePath) && end(explode('.', $filePath)) == 'php') {

            // 取得檔案內容
            $fcontent = file_get_contents($filePath);

            // 找到namespace
            $chkNs = preg_match_all('/\nnamespace (.*);\n/', $fcontent, $ns);
            $ns = trim($ns[1][0], ' ');
            // print($ns.'<br>');

            // 找到classname
            $chkCn = preg_match_all('/class (.*?)(?:extends|im|\n|{)/', $fcontent, $cn);
            $cn = trim($cn[1][0], ' ');
            // print($cn.'<br>');

            // 確認正確後引入
            if ($ns.'\\'.$cn == $cname || $cn == $cname) {
                include $filePath;
                return true;
            }
        }
    }
    return $isFind;
}

/**
 * 自動加載 註冊至spl_autoload_register
 * ref. https://www.php.net/manual/en/function.spl-autoload-register.php
 *
 * @param $className
 */
spl_autoload_register(
    function ($className)
    {
        $coreFiles = require 'classmap.php';
        if (isset($coreFiles[$className])) {
            // 內建Class 從classmap中尋找
            $classFile = $coreFiles[$className];
            include_once $classFile;
            return;
        }

        $appDirs = APP_DEFAULT_DIRS;
        foreach ($appDirs as $dir) {
            if (findAppPath($className, $dir)) return;
        }
    }
);

function dd($var) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($var);
    die();
}