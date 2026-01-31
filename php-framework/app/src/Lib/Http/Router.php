<?php

namespace App\Lib\Http;

use App\Lib\Controllers\AbstractController;


class Router {

    const string CONTROLLER_NAMESPACE_PREFIX = "App\\Controllers\\";
    const string ROUTE_CONFIG_PATH = __DIR__ . '/../../../config/routes.json';
    

    public static function route(Request $request): Response {
        $config = self::getConfig(); //routes.json but array

        foreach($config as $route) {
            if(self::checkMethod($request, $route) === false || self::checkUri($request, $route) === false) {
                continue;
            }

            $params =self::matchUriParams($request, $route);
            if ($params === false) {
                continue;
            }

            $request->setParams($params);

            $controller = self::getControllerInstance($route['controller']);
            return $controller->process($request);
        }

        throw new \Exception('Route not found', 404);
    }
    
    private static function getConfig(): array {
        $routesConfigContent = file_get_contents(self::ROUTE_CONFIG_PATH);
        $routesConfig = json_decode($routesConfigContent, true);

        return $routesConfig;
    } //get route.json

    private static function checkMethod(Request $request, array $route): bool {
        return $request->getMethod() === $route['method'];
    }

    private static function checkUri(Request $request, array $route): bool {
        return $request->getUri() === $route['path'];
    }

    private static function matchUriParams(Request $request, array $route): array|bool {
        $path = $route['path'];

        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $request->getUri(), $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        return false;
    }


    private static function getControllerInstance(string $controller): AbstractController {
        $controllerClass = self::CONTROLLER_NAMESPACE_PREFIX . $controller;

        if(class_exists($controllerClass) === false) {
            throw new \Exception('Route not found', 404);
        }

        $controllerInstance = new $controllerClass();

        if(is_subclass_of($controllerInstance, AbstractController::class)=== false){
            throw new \Exception('Route not found', 404);
        }
        
        return $controllerInstance;
    }

}
