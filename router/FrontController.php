<?php

require_once dirname(__DIR__) . "/controllers/HomeController.php";
require_once dirname(__DIR__) . "/controllers/ArtistController.php";
require_once dirname(__DIR__) . "/controllers/ArtworkController.php";
require_once dirname(__DIR__) . "/controllers/GenreController.php";
require_once dirname(__DIR__) . "/controllers/SubjectController.php";
require_once dirname(__DIR__) . "/controllers/AuthController.php";
require_once dirname(__DIR__) . "/controllers/AdminController.php";
require_once dirname(__DIR__) . "/controllers/SearchController.php";
require_once dirname(__DIR__) . "/controllers/ReviewController.php";
require_once dirname(__DIR__) . "/controllers/ErrorController.php";

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
                '/edit-profile' => ['AuthController', 'editProfile'],
                '/edit-profile/{id}' => ['AuthController', 'editProfile'],
                '/change-password' => ['AuthController', 'changePassword'],
                '/search' => ['SearchController', 'search'],
                '/advanced-search' => ['SearchController', 'advancedSearch'],
            ],
            'POST' => [
                '/login' => ['AuthController', 'processLogin'],
                '/register' => ['AuthController', 'processRegister'],
                '/manage-users' => ['AdminController', 'manageUsers'],
                '/edit-profile' => ['AuthController', 'updateProfile'],
                '/edit-profile/{id}' => ['AuthController', 'updateProfile'],
                '/change-password' => ['AuthController', 'updatePassword'],
                '/favorites/artists/{id}/toggle' => ['AuthController', 'toggleArtistFavoriteAjax'],
                '/favorites/artworks/{id}/toggle' => ['AuthController', 'toggleArtworkFavoriteAjax'],
                '/reviews/add' => ['ReviewController', 'addReview'],
                '/reviews/{id}/delete' => ['ReviewController', 'deleteReview'],
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
            // Check if this is a static file or asset request
            if ($this->isStaticFileRequest($uri)) {
                return false; // Let server handle static files
            }
            
            // Show 404 page for all other requests
            $errorController = new ErrorController();
            $errorController->notFound();
            return true;
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
            // Handle both {id} for regular routes and {id} for API routes
            $regex = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
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
    
    private function isStaticFileRequest($uri)
    {
        // List of static file extensions and paths that should be served directly
        $staticPaths = [
            '/assets/',
            '/favicon.ico',
            '/robots.txt'
        ];
        
        $staticExtensions = [
            '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', 
            '.ico', '.woff', '.woff2', '.ttf', '.eot', '.pdf'
        ];
        
        // Check if URI starts with static paths
        foreach ($staticPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }
        
        // Check if URI has static file extension
        foreach ($staticExtensions as $ext) {
            if (substr($uri, -strlen($ext)) === $ext) {
                return true;
            }
        }
        
        return false;
    }
}
