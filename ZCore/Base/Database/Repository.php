<?php
namespace Core\Base;

use Core\Base\BaseObj;
use Core\Base\Model;

class Repository extends BaseObj
{
    public Model $model;

    public function __construct()
    {
        parent::__construct();
    }
}
