<?php

namespace phpessential\mvc\controllers;

/**
 * Base controller class, used for handle http request
 */
class Controller {

    /**
     * Send HTTP 500
     */
    protected function internalServerError() {
        header("HTTP/1.1 500 Internal server error", true, 500);
        exit();
    }

    /**
     * Send HTTP 404
     */
    protected function notFound() {
        header("HTTP/1.1 404 Not Found", true, 404);
        exit();
    }

    /**
     * Send HTTP 401
     */
    protected function unauthorized() {
        header("HTTP/1.1 401 Unauthorized", true, 401);
        exit();
    }

    /**
     * Send HTTP 400
     */
    protected function badRequest() {
        header("HTTP/1.1 400 Bad request", true, 400);
        exit();
    }

    /**
     * Redirect to specifc route
     *
     * @param string $route
     *        	route vers laquelle rediriger
     */
    protected function redirection(string $route) {
        header("Location:" . $route);
        exit();
    }

    /**
     * Send json to client
     *
     * @param mixed $objectToSend
     *        	objet à transformer en json et à envoyer au client
     */
    protected function sendJson($objectToSend, bool $stripNulls = false) {
        $json = json_encode($stripNulls ? array_filter((array) $objectToSend) : $objectToSend);
        if ($json == false) {
            throw new \Exception(json_last_error_msg());
        } else {
            echo $json;
            exit();
        }
    }

}
