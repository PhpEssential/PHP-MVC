<?php

namespace phpessential\mvc;

use phpessential\mvc\utils\LogUtils;

/**
 * Url route management
 */
class Route {
    private static $instance = null;
    private $routeMap = array();

    /**
     * Create the route map here
     */
    protected function __construct() {

    }

    protected function addRouteMap(string $url, $function) {
        $this->routeMap [$url] = $function;
    }

    public static function getInstance(): Route {
        if (self::$instance == null) {
            $class = static::class;
            self::$instance = new $class();
        }
        return self::$instance;
    }

    public function runController(string $url) {
        if (!array_key_exists($url, $this->routeMap)) {
            self::notFound();
        }

        $function = $this->routeMap [$url];
        try {
            $function();
        } catch (\Exception $e) {
            LogUtils::error($e);
            Route::internalServerError();
        }
    }

    /**
     * Crée une url depuis une route
     *
     * @param string $route
     *        	route à transformer
     *
     * @return string l'url correspondante
     */
    public static function createRoute(string $route): string {
        return self::createParameterizedRoute($route);
    }

    /**
     * Crée une url avec des paramètres depuis une route
     *
     * @param string $route
     *        	route à transformer
     * @param array $params
     *        	paramètres à incorporer
     *
     * @return string l'url correspondante
     */
    public static function createParameterizedRoute(string $route, array $params = array()): string {
        $urlParams = "";
        foreach ($params as $key => $value) {
            $urlParams .= $key . "=" . $value . "&";
        }
        return self::createUrlFromRoot($route . ($urlParams != "" ? "?" . substr($urlParams, 0, strlen($urlParams) - 1) : ""));
    }

    /**
     * Permet de rajouter le domaine de site web à une url
     *
     * @param string $url
     *
     * @return string
     */
    public static function createUrlFromRoot(string $url): string {
        return Config::get(Config::ROOT_URL) . "/" . $url;
    }

}
