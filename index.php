<?php

// 此目錄
define('APP_PATH', __DIR__);

// 核心目錄
define('CORE_PATH', __DIR__.'/ZCore');

// 是否啟用 server message
define('DEBUG', true);

// 預設應用資料夾設定
include_once APP_PATH.'/config/app.php';

// 預設資料庫設定
include_once APP_PATH.'/config/db.php';

// 自動引入
include_once CORE_PATH.'/_boot/autoload.php';

// 路由對應
include_once APP_PATH.'/config/route.php';

// 執行核心
(new Handlers\Handler(
    new Core\Base\Action('Core\Core', 'run')))->run();

