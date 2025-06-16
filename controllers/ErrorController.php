<?php

require_once __DIR__ . "/BaseController.php";

class ErrorController extends BaseController
{
    public function notFound()
    {
        // Set proper HTTP status code
        http_response_code(404);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $data = [
            'title' => '404 - Page Not Found - Art Gallery'
        ];
        
        echo $this->renderWithLayout('errors/404', $data);
    }
    
    public function serverError()
    {
        // Set proper HTTP status code
        http_response_code(500);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $data = [
            'title' => '500 - Server Error - Art Gallery'
        ];
        
        echo $this->renderWithLayout('errors/500', $data);
    }
}
