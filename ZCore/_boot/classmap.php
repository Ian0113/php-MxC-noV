<?php
return [

    /**
     * 執行核心
     */
    'Core\Core'                                     => CORE_PATH.'/Core.php',

    /**
     * 基本輸入輸出
     */
    'Core\Base\Request'                             => CORE_PATH.'/Base/InOut/Request.php',
    'Core\Base\Response'                            => CORE_PATH.'/Base/InOut/Response.php',

    /**
     * static
     */
    'Core\Base\Session'                             => CORE_PATH.'/Base/Static/Session.php',
    'Core\Base\Route'                               => CORE_PATH.'/Base/Static/Route.php',

    /**
     * 物件
     */
    'Core\Base\BaseObj'                             => CORE_PATH.'/Base/BaseObj.php',
    'Core\Base\BaseIOObj'                             => CORE_PATH.'/Base/BaseIOObj.php',
    'Core\Base\BaseDBObj'                           => CORE_PATH.'/Base/BaseDBObj.php',

    /**
     * app
     */
    'Core\Base\Controller'                          => CORE_PATH.'/Base/App/Controller.php',
    'Core\Base\Middleware'                          => CORE_PATH.'/Base/App/Middleware.php',

    /**
     * db
     */
    'Core\Base\Model'                               => CORE_PATH.'/Base/Database/Model.php',
    'Core\Base\Repository'                          => CORE_PATH.'/Base/Database/Repository.php',

    /**
     * handle
     */
    'Core\Base\Action'                              => CORE_PATH.'/Base/Handle/Action.php',
    'Core\Base\Handler'                             => CORE_PATH.'/Base/Handle/Handler.php',

    /**
     * 錯誤/意外相關
     */
    'Core\Exceptions\CoreException'                 => CORE_PATH.'/Base/Handle/Exceptions/CoreException.php',
    'Core\Exceptions\MiddlewareException'           => CORE_PATH.'/Base/Handle/Exceptions/MiddlewareException.php',

];