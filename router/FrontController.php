<?php

require_once dirname(__DIR__) . "/controllers/HomeController.php";
require_once dirname(__DIR__) . "/controllers/ArtistController.php"; 
require_once dirname(__DIR__) . "/controllers/ArtworkController.php";
require_once dirname(__DIR__) . "/controllers/GenreController.php";
require_once dirname(__DIR__) . "/controllers/SubjectController.php";
require_once dirname(__DIR__) . "/controllers/AuthController.php";
require_once dirname(__DIR__) . "/controllers/AdminController.php";

class FrontController
{
    private $routes = [];
    
    public function __construct()
    {
        $this->setupRoutes();
    }
    
    private function setupRoutes()
    {
        // Define controller routes
        $this->routes = [
            'GET' => [
                '/' => ['HomeController', 'index'],
                '/index' => ['HomeController', 'index'],
                '/about' => ['HomeController', 'about'],
                '/artists' => ['ArtistController', 'index'],
                '/artists/{id}' => ['ArtistController', 'show'],
                '/artworks' => ['ArtworkController', 'index'],
                '/artworks/{id}' => ['ArtworkController', 'show'],
                '/genres' => ['GenreController', 'index'],
                '/genres/{id}' => ['GenreController', 'show'],
                '/subjects' => ['SubjectController', 'index'],
                '/subjects/{id}' => ['SubjectController', 'show'],
                '/login' => ['AuthController', 'showLogin'],
                '/register' => ['AuthController', 'showRegister'],
                '/account' => ['AuthController', 'showAccount'],
                '/favorites' => ['AuthController', 'showFavorites'],
                '/logout' => ['AuthController', 'logout'],
                '/manage-users' => ['AdminController', 'manageUsers'],
            ],
            'POST' => [
                '/login' => ['AuthController', 'processLogin'],
                '/register' => ['AuthController', 'processRegister'],
            ]
        ];
    }
    
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        // Find matching route
        $routeInfo = $this->findRoute($method, $uri);
        
        if (!$routeInfo) {
            // Route not found - let the original file-based routing handle it
            return false;
        }
        
        [$controllerName, $action, $params] = $routeInfo;
        
        // Instantiate controller and call action
        $controller = new $controllerName();
        
        if (method_exists($controller, $action)) {
            if (!empty($params)) {
                call_user_func_array([$controller, $action], $params);
            } else {
                $controller->$action();
            }
            return true;
        }
        
        return false;
    }
    
    private function findRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $pattern => $handler) {
            $params = [];
            
            // Convert route pattern to regex
            $regex = preg_replace('/\{(\w+)\}/', '(\d+)', $pattern);
            $regex = '#^' . $regex . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                // Extract parameters
                array_shift($matches); // Remove full match
                $params = $matches;
                
                return [$handler[0], $handler[1], $params];
            }
        }
        
        return null;
    }
}
