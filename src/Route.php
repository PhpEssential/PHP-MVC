<?php
namespace phpessential\mvc;

use phpessential\mvc\utils\LogUtils;

/**
 * Url route management
 */
class Route {
	private static $instance = null;
	private $routeMap = array ();

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
		if (! array_key_exists($url, $this->routeMap)) {
			self::notFound();
		}

		$function = $this->routeMap [$url];
		try {
			$function();
		} catch ( \Exception $e ) {
			LogUtils::error($e);
			Route::internalServerError();
		}
	}

	/**
	 * Return a responde code 500
	 */
	public static function internalServerError() {
		header("HTTP/1.1 500 Internal server error", true, 500);
		exit();
	}

	/**
	 * Return a response code 404
	 */
	public static function notFound() {
		header("HTTP/1.1 404 Not Found", true, 404);
		exit();
	}

	/**
	 * Retourne un code HTTP 401
	 */
	public static function unauthorized() {
		header("HTTP/1.1 401 Unauthorized", true, 401);
		exit();
	}

	/**
	 * Retourne un code HTTP 400
	 */
	public static function badRequest() {
		header("HTTP/1.1 400 Bad request", true, 400);
		exit();
	}

	/**
	 * Redirection vers une route
	 *
	 * @param string $route
	 *        	route vers laquelle rediriger
	 */
	public static function redirection(string $route) {
		header("Location:" . $route);
		exit();
	}

	/**
	 * Redirection vers une route
	 *
	 * @param mixed $objectToSend
	 *        	objet à transformer en json et à envoyer au client
	 */
	public static function sendJson($objectToSend) {
		echo json_encode($objectToSend);
		exit();
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
		foreach ( $params as $key => $value ) {
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