<?php
namespace App\Controllers;

use Core\Base\Controller;
use Core\Base\Route;

class HomeController extends Controller
{
    public function getRouteList()
    {
        $this->getResponse()->setData('routelist', Route::getList());
    }
}
