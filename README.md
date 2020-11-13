# php-MxC-noV

## 需求
* PHP >= 5.4
* Nginx | Appache
* MariaDB | MySQL

## 使用
### docker-compose 建置 [docker-lnmp](https://github.com/fizz0113/docker-lnmp)
### 新增控制器
>範例:
>
>在App/Controllers新增HomeController。
>```php
>namespace App\Controllers;
>use Core\Base\Controller;
>class HomeController extends Controller
>{
>    public function index()
>    { // code...
>    }
>}
>```
### 新增中間層。
>範例:
>
>在App/Middlewares內新增HomeMiddleware。
>```php
>namespace App\Middlewares;
>use Core\Base\Middleware;
>use Core\Exceptions\MiddlewareException;
>class HomeMiddleware extends Middleware
>{
>    // 驗證標頭是否有 token
>    public array $header = [
>        'token' => null,
>    ];
>
>    public array $data = [
>    ];
>
>    public function run()
>    {
>        parent::run(); // 內建自動會驗 $header 及 $data
>        // 驗證執行區
>        throw new MiddlewareException("有?", 404);
>    }
>}
>```
### 設定Route
> 範例:
>
> 在 /config/route.php新增路由。
> ```php
> use Core\Base\Route;
> Route::get('/home', 'App\Controllers\HomeController', ['HomeMiddleware']);

## 資料夾概述
### 整體
```
/
├─── App................................ 應用
|       ├─── Controllers................ 控制器
|       └─── Middlewares................ 中間層
|               └─── Core............... Core內建middleware
├─── DataBase........................... 資料庫
|       ├─── Models..................... 模型
|       └─── Repositories............... 集成資源
├─── Handlers........................... 主要程序處理
|       └─── Exceptions................. 客制意外
├─── config............................. 設定檔
└─── ZCore.............................. 核心
```

### ZCore
```
/ZCore.................................. 核心
├─── /_boot............................. 自動載入
└─── /Base.............................. 基礎物件
        ├─── /App....................... 應用繼承物件 Controller / Middleware
        ├─── /Database.................. 資料庫繼承物件 Model / Repository
        ├─── /Handle.................... 程序處理
        |       └─── /Exceptions........ 意外
        ├─── /InOut..................... 網頁基本輸入輸出
        └─── /Static.................... 靜態類別
```

## 設定

### /config/app.php
在此處定義應用資料夾及資料庫資料夾設定。
```php
define('APP_DEFAULT_DIRS', [
    APP_PATH.'/Handlers',
    APP_PATH.'/App/Middlewares',
    APP_PATH.'/App/Controllers',
    APP_PATH.'/App/Others',
    APP_PATH.'/DataBase/Repositories',
    APP_PATH.'/DataBase/Models',
]);
```
### /config/db.php
在此處修改資料庫基本設定。
```php
define('DB_HOST', 'mariadb');
define('DB_PORT', 3306);
define('DB_NAME', 'app_test');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
```

### /config/route.php
在此處定義路由設定。
```php
use Core\Base\Route;
Route::group('/api', [
    Route::group('/post', [
        Route::middleware('App\Middlewares\TokenMiddleware', [
            // 需要 token 才能訪問
            Route::middleware('App\Middlewares\AuthMiddleware', [
                // 需要登入才能訪問
            ]),
            Route::group('/sign', [
                // 登入 | 註冊 | 登出
                Route::post('In', 'App\Controllers\SignController@signIn', ['App\Middlewares\SignInMiddleware']),
                Route::post('Up', 'App\Controllers\SignController@signUp', ['App\Middlewares\SignUpMiddleware']),
                Route::post('Out', 'App\Controllers\SignController@signOut'),
            ]),
        ]),
    ]),
]);
```


## 細項概述

### index.php
網頁開始執行時會從此處載入設定後透過Handler執行Core。
```php
define('APP_PATH', __DIR__);
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

```

### Handlers\Handler
> 在此處執行Core並接收意外問題。
>
> 主要執行 request -> routing->middlewares->controller **->response**。
```php
class Handler extends BaseHandler
{
    public function run()
    {
        try {
            parent::run(); // Core執行
        } catch (MiddlewareException $middlewareExp) {
            // 中間層驗證問題
        } catch (CoreException $coreExp) {
            // 核心問題
        } catch (\Throwable $th) {
            // 其他
        }
    }
}
```

### Core\Core
> 此處執行 路由-中間層-控制器。
>
> 主要執行 request -> **routing->middlewares->controller** -> response。
```php
class Core extends BaseIOObj
{
    public function __construct()
    {
        parent::__construct();
    }
    // 由index.php進入這執行已定義動作
    public function run()
    {
        // 1.找路由列表
        // 2.透過 Middleware 驗證 Request
        // 3.執行 Controller
        // 執行完畢會在Handler結束
    }
}
```

### Core\InOut
Request 及 Response會在 Core\Base\BaseIOObj 中實例。
```php
class Request
{
    // 取得訪問 Uri/method/header/data(json)/time
    public function getUri(): string{}
    public function getMethod(): string{}
    public function getHeader(): array{}
    public function getData(): array{}
    public function getTime(): float{}
}
class Response
{
    // 設定回應時的 header/data(json)
    public function setHeader(string $var): void{}
    public function setData(string $indexName, $var): void{}
    public function setServerMsg(string $indexName, $var): void{}
    public function setAccess(bool $mod = true): void{}
    public function render(int $errCode = null){}
}
```

### Core\Base
> BaseObj 此類別會記錄執行路徑。
>> 子類別
>> * Core\Base\BaseIOObj
>
> BaseIOObj 此類別繼承自 BaseObj 並引入 Core\InOut\Request 及 Core\InOut\Reqponse 放入靜態變數共用。
>> 子類別
>> * Core\Core
>> * Core\Base\Handle
>> * Core\Base\Controller
>> * Core\Base\Middleware
>
> BaseDBObj 此類別使用 PDO 連接 DB。
>> 子類別
>> * Core\Base\Model

```php
class BaseObj
{
    //紀錄執行路徑
    private static array $execList = [];
    private static array $nowExec;
    protected array $preExec;
}
class BaseIOObj extends BaseObj
{
    // 繼承會共用 static
    private static Response $response;
    private static Request $request;
}
class BaseDBObj
{
    protected static ?\PDO $pdo;
    // 使用 pdo 連接 db
    protected static function DBConnect(){}
    // 釋放 pdo 資源
    protected static function DBDisconnect(){}
}
```



## 參考
> https://laravel.com/docs/5.8/routing#route-parameters
>
>https://www.php.net/manual/en/function.call-user-func-array.php
>
>https://www.php.net/manual/en/reflectionfunctionabstract.getparameters.php
>
>https://www.php.net/manual/en/function.spl-autoload-register.php