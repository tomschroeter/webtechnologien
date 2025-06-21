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

/**
 * Handles routing for incoming HTTP requests
 * to the appropriate controller and action based on defined routes.
 */
class FrontController
{
    private array $routes = [];

    public function __construct()
    {
        $this->setupRoutes();
    }

    /**
     * Sets up the routes mapping URI patterns to controller actions.
     *
     * @return void
     */
    private function setupRoutes(): void
    {
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
                '/change-password/{id}' => ['AuthController', 'updatePassword'],
                '/favorites/artists/{id}/toggle' => ['AuthController', 'toggleArtistFavoriteAjax'],
                '/favorites/artworks/{id}/toggle' => ['AuthController', 'toggleArtworkFavoriteAjax'],
                '/reviews/add' => ['ReviewController', 'addReview'],
                '/reviews/{id}/delete' => ['ReviewController', 'deleteReview'],
            ]
        ];
    }

    /**
     * Dispatches the current HTTP request to the appropriate controller and action.
     *
     * @return bool True if the request was handled; false if it should be handled elsewhere.
     * @throws HttpException If the route or action is not found.
     */
    public function dispatch(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Normalize URI
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        $routeInfo = $this->findRoute($method, $uri);

        if (!$routeInfo) {
            if ($this->isStaticFileRequest($uri)) {
                return false;
            }
            throw new HttpException(404, "The requested page was not found.");
        }

        [$controllerName, $action, $params] = $routeInfo;

        $controller = new $controllerName();

        if (method_exists($controller, $action)) {
            if (!empty($params)) {
                call_user_func_array([$controller, $action], $params);
            } else {
                $controller->$action();
            }
            return true;
        }

        throw new HttpException(404, "The requested action was not found.");
    }

    /**
     * Finds a matching route based on HTTP method and URI.
     *
     * @param string $method HTTP request method (e.g., GET, POST)
     * @param string $uri URI path of the request
     * @return array|null Array of [controllerName, action, parameters] or null if no match
     */
    private function findRoute(string $method, string $uri): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            $regex = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                return [$handler[0], $handler[1], $matches];
            }
        }

        return null;
    }

    /**
     * Determines if the requested URI targets a static file.
     *
     * @param string $uri The request URI path
     * @return bool True if it's a static file request; otherwise, false
     */
    private function isStaticFileRequest(string $uri): bool
    {
        $staticPaths = [
            '/assets/',
            '/favicon.ico',
        ];

        $staticExtensions = [
            '.css',
            '.js',
            '.png',
            '.jpg',
            '.jpeg',
            '.gif',
            '.svg',
            '.ico',
            '.woff',
            '.woff2',
            '.ttf',
            '.eot',
            '.pdf'
        ];

        foreach ($staticPaths as $path) {
            if (strpos($uri, $path) === 0) {
                return true;
            }
        }

        foreach ($staticExtensions as $ext) {
            if (substr($uri, -strlen($ext)) === $ext) {
                return true;
            }
        }

        return false;
    }
}
