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
require_once dirname(__DIR__) . "/exceptions/HttpException.php";

// Front Controller class responsible for routing incoming HTTP requests
// to the apropriate controller and action based on defined routes.
class FrontController
{
    private $routes = [];
    
    public function __construct()
    {
        $this->setupRoutes();
    }
    
    // Defines mappings between url patterns and their corresponding controller/action pairs
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
                '/manage-users' => ['AdminController', 'handleUserAction'],
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
    
    // Parses the request uri, finds matching route, and redirects to controller.
    // Returns true if request was handled, false if it should be handled elsewhere
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
                return false; // Let the normal server handle static files
            }
            
            // Throw 404 exception instead of handling it here
            throw new HttpException(404, "The requested page was not found.");
        }
        
        [$controllerName, $action, $params] = $routeInfo;
        
        // Instantiate controller and call action
        $controller = new $controllerName();
        
        if (method_exists($controller, $action)) {
            if (!empty($params)) {
                // Use call_user_func_array to dynamically call the method with parameters
                call_user_func_array([$controller, $action], $params);
            } else {
                // No parameters needed, call method directly
                $controller->$action();
            }
            return true;
        }
        
        // Method doesn't exist - this is a 404
        throw new HttpException(404, "The requested action was not found.");
    }
    
    // This finds the route for the http method and uri
    // and supports parameterized routes with placeholders like {id}
    // Parameters: method (http method like get, post), uri (request uri path)
    // returns [controllerName, action, parameters] if found, null otherwise
    private function findRoute($method, $uri)
    {
        // Check if there are any routes defined for this HTTP method (GET, POST...)
        if (!isset($this->routes[$method])) {
            return null; // No routes for this method --> return null
        }
        
        // Loop through each route pattern for this HTTP method
        // $pattern is something like '/artists/{id}', $handler is ['ArtistController', 'show']
        foreach ($this->routes[$method] as $pattern => $handler) {
            $params = []; // Initialize empty array to store extracted parameters
            
            // Convert route pattern into a regular expression for matching
            // Example: '/artists/{id}' --> becomes '/artists/([^/]+)'
            // The {id} placeholder gets replaced with ([^/]+) which matches any characters except /
            $regex = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
            
            // Add regex delimiters and anchors
            // Example: '/artists/([^/]+)' becomes '#^/artists/([^/]+)$#'
            // ^ means start of string, $ means end of string, # are delimiters
            $regex = '#^' . $regex . '$#';
            
            // Test if the uri matchbs this route pattern
            // If it matches, $matches will contain the full match and any captured groups
            if (preg_match($regex, $uri, $matches)) {
                // Extract the parameter values from the matched groups
                // $matches[0] is the full match, $matches[1], $matches[2]... are the captured parameters
                array_shift($matches); // Remove the full match, keep only the parameter values
                $params = $matches; // Store the parameter values
                
                // Return array with: [controller class name, method name, parameters]
                // Example: ['ArtistController', 'show', ['123']]
                return [$handler[0], $handler[1], $params];
            }
        }
        
        // No matching route found for this URI
        return null;
    }
    
    // Determines if the requested uri is for a static file (css, js, images...)
    // that should be served directly by the web server and not processed by the application
    private function isStaticFileRequest($uri)
    {
        // List of static file extensions and paths that should be served directly
        $staticPaths = [
            '/assets/',
            '/favicon.ico',
        ];
        
        $staticExtensions = [
            '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', 
            '.ico', '.woff', '.woff2', '.ttf', '.eot', '.pdf'
        ];
        
        // Check if uri starts with static paths
        foreach ($staticPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }
        
        // Check if uri has static file extension
        foreach ($staticExtensions as $ext) {
            if (substr($uri, -strlen($ext)) === $ext) {
                return true;
            }
        }
        
        return false;
    }
}
