<?php

namespace Core\Base;

use Core\Base\Request;
use Core\Base\Response;
use Core\Base\BaseObj;

class BaseIOObj extends BaseObj
{

    /**
     * http response
     * @var Response
     */
    private static Response $response;
    public static function getResponse()
    {
        return self::$response;
    }

    /**
     * http request
     * @var Request
     */
    private static Request $request;
    public static function getRequest()
    {
        return self::$request;
    }

    public function __construct()
    {
        parent::__construct();
        if (!isset(self::$response)) {
            self::$response = new Response;
        }
        if (!isset(self::$request)) {
            self::$request = new Request;
        }
    }

}
