<?php
namespace Core\Base;

use Core\Exceptions\CoreException;

class Route
{
    private static array $routeList = [
        'get'       => [],
        'post'      => [],
        'put'       => [],
        'update'    => [],
        'delete'    => [],
    ];

    /**
     * 動態function
     */
    public static function __callStatic($name, $args)
    {
        if ($name == 'get' || $name == 'post' || $name == 'put' || $name == 'update' || $name == 'delete') {
            $route = [
                $args[0] => [
                    'controller'        => $args[1],
                    'middlewares'       => !isset($args[2]) ? [] : $args[2],
                ],
            ];
            self::$routeList[$name] = array_merge(self::$routeList[$name], $route);
            return ['route_pair' => [$name, $args[0]]];
        } elseif ($name == 'middleware') {
            $routePairList = [];
            foreach ($args[1] as $arr) {
                if (!isset($arr['route_pair'])) {
                    foreach ($arr as $routePair) {
                        $route = $routePair['route_pair'];
                        self::$routeList[$route[0]][$route[1]]['middlewares']
                        = array_merge(
                            self::$routeList[$route[0]][$route[1]]['middlewares'],
                            (is_array($args[0]) ? $args[0] : [$args[0]])
                        );
                        $routePairList = array_merge($routePairList, [['route_pair' => $route]]);
                    }
                    continue;
                }
                $route = $arr['route_pair'];
                self::$routeList[$route[0]][$route[1]]['middlewares'] = array_merge(
                    self::$routeList[$route[0]][$route[1]]['middlewares'],
                    (is_array($args[0]) ? $args[0] : [$args[0]])
                );
                $routePairList = array_merge($routePairList, [['route_pair' => $route]]);
            }
            return $routePairList;
        } elseif ($name == 'group') {
            $routePairList = [];
            foreach ($args[1] as $arr) {
                if (!isset($arr['route_pair'])) {
                    foreach ($arr as $routePair) {
                        $route = $routePair['route_pair'];
                        self::$routeList[$route[0]][$args[0].$route[1]] = self::$routeList[$route[0]][$route[1]];
                        unset(self::$routeList[$route[0]][$route[1]]);
                        $route[1] = $args[0].$route[1];
                        $routePairList = array_merge($routePairList, [['route_pair' => $route]]);
                    }
                    continue;
                }
                $route = $arr['route_pair'];
                self::$routeList[$route[0]][$args[0].$route[1]] = self::$routeList[$route[0]][$route[1]];
                unset(self::$routeList[$route[0]][$route[1]]);
                $route[1] = $args[0].$route[1];
                $routePairList = array_merge($routePairList, [['route_pair' => $route]]);
            }
            return $routePairList;
        }
        elseif ($name == 'getList') {
            $method = $args[0];
            if ($method == '' || $method == null) {
                return self::$routeList;
            }
            return self::$routeList[strtolower($method)];
        }
        throw new CoreException("在靜態類別 'Route' 中無此方法 '$name'", 500);
    }

    /**
     * 會將變數設定進 $routeList
     */
    private static function setRouteList(string $index, $uri, $controller, $middlewares)
    {
        self::$routeList[$index] = array_merge(self::$routeList[$index], [
            $uri => [
                'controller'        => $controller,
                'middlewares'       => $middlewares,
            ]
        ]);
    }
}
