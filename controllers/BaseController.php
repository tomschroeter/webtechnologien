<?php

/**
 * Abstract base controller class providing shared functionality for all controllers.
 */
abstract class BaseController
{
    /**
     * Renders a view and returns its contents as a string.
     *
     * @param string $view  Path to the view file (relative to /views).
     * @param array  $data  Associative array of data to be extracted for the view.
     *
     * @throws Exception    If the view file does not exist.
     */
    protected function render($view, $data = []): bool|string
    {
        // Make the array keys available as variables in the view
        extract($data);

        // Start output buffering
        ob_start();

        // Build full path to the view file
        $viewPath = dirname(__DIR__) . "/views/{$view}.php";

        // Check if view exists
        if (!file_exists($viewPath)) {
            throw new Exception("View '{$view}' not found at {$viewPath}");
        }

        // Include the view file
        require $viewPath;

        // Get and clean the buffered output
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Renders a view wrapped inside a layout, and outputs the final result.
     *
     * @param string $view    View file to render.
     * @param array  $data    Data to pass to the view and layout.
     * @param string $layout  Layout file to wrap around the content.
     */
    protected function renderWithLayout($view, $data = [], $layout = 'layouts/main')
    {
        // Fetch notifications for display if not already passed
        if (!isset($data['notifications'])) {
            $data['notifications'] = $this->getNotifications();
        }

        // Render the inner view content
        $content = $this->render($view, $data);

        // Inject content into layout data
        $data['content'] = $content;

        // Render and output layout
        echo $this->render($layout, $data);
        exit(); // Ensure no further output is sent
    }

    /**
     * Redirects to a given URL.
     *
     * @param string $url  Destination URL.
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }

    /**
     * Redirects to a URL with a single flash notification message.
     *
     * @param string $url      Destination URL.
     * @param string $message  Message content.
     * @param string $type     Notification type (e.g., success, error, info).
     */
    protected function redirectWithNotification($url, $message, $type = 'success')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['notifications'] = [['message' => $message, 'type' => $type]];

        $this->redirect($url);
    }

    /**
     * Redirects to a URL with multiple notification messages.
     *
     * @param string $url           Destination URL.
     * @param array  $notifications Array of ['message' => ..., 'type' => ...].
     */
    protected function redirectWithNotifications($url, $notifications)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['notifications'] = $notifications;

        $this->redirect($url);
    }

    /**
     * Retrieves and clears notification messages from the session.
     *
     * @return array  Array of notification messages.
     */
    protected function getNotifications(): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $notifications = $_SESSION['notifications'] ?? [];

        // Clear notifications after retrieval
        unset($_SESSION['notifications']);

        return $notifications;
    }

    /**
     * Legacy method for retrieving a single flash message.
     * Converts it to the newer notifications array format.
     */
    protected function getFlashMessage(): array|null
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = $_SESSION['flash_message'] ?? null;
        $type = $_SESSION['flash_type'] ?? 'info';

        // Clear flash messages
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);

        return $message ? ['message' => $message, 'type' => $type] : null;
    }

    /**
     * Gets the currently logged-in user's ID from the session.
     */
    protected function getCurrentUser(): int|null
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['customerId'] ?? null;
    }

    /**
     * Redirects to login page if user is not authenticated.
     */
    protected function requireAuth(): void
    {
        if (!$this->getCurrentUser()) {
            $this->redirect('/login');
        }
    }

    /**
     * Sends a JSON response with a given status code.
     *
     * @param mixed $data        Data to be encoded as JSON.
     * @param int   $statusCode  HTTP status code to return.
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
