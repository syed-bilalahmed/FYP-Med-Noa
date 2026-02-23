<?php

class Router {
    public function run() {
        $url = isset($_GET['route']) ? $_GET['route'] : 'auth/login';
        // Ensure query strings are stripped from route if they appear
        $url = explode('?', $url)[0];
        $parts = explode('/', $url);
        
        $controllerName = str_replace('_', '', ucwords($parts[0], '_')) . 'Controller';
        $methodName = isset($parts[1]) ? $parts[1] : 'index';

        // Check if controller file exists
        if (file_exists("controllers/" . $controllerName . ".php")) {
            require_once "controllers/" . $controllerName . ".php";
            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                echo "Method not found: $methodName";
            }
        } else {
            echo "Controller not found: $controllerName";
        }
    }
}
