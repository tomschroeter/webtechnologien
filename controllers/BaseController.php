<?php

abstract class BaseController
{
    protected function render($view, $data = [])
    {
        // Extract data array to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = dirname(__DIR__) . "/views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new Exception("View '{$view}' not found at {$viewPath}");
        }
        
        require $viewPath;
        
        // Get the contents and clean the buffer
        $content = ob_get_clean();
        
        // Return the rendered content
        return $content;
    }
    
    protected function renderWithLayout($view, $data = [], $layout = 'layouts/main')
    {
        // Automatically get flash message if not already provided
        if (!isset($data['flashMessage'])) {
            $data['flashMessage'] = $this->getFlashMessage();
        }
        
        // Render the view content first
        $content = $this->render($view, $data);
        
        // Add content to data for layout
        $data['content'] = $content;
        
        // Render with layout and output directly
        echo $this->render($layout, $data);
        exit(); // Always exit after rendering to prevent further output
    }
    
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }
    
    protected function redirectWithMessage($url, $message, $type = 'success')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
        
        $this->redirect($url);
    }
    
    protected function getFlashMessage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $message = $_SESSION['flash_message'] ?? null;
        $type = $_SESSION['flash_type'] ?? 'info';
        
        // Clear the flash message
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $message ? ['message' => $message, 'type' => $type] : null;
    }
    
    protected function getCurrentUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['customerId'] ?? null;
    }
    
    protected function requireAuth()
    {
        if (!$this->getCurrentUser()) {
            $this->redirect('/login');
        }
    }
    
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
