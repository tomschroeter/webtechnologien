<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class AuthController extends BaseController
{
    private $db;
    private $customerRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->customerRepository = new CustomerLogonRepository($this->db);
    }
    
    public function showLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'Login - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/login', $data);
    }
    
    public function showRegister()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'Register - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/register', $data);
    }
    
    public function showAccount()
    {
        $this->requireAuth();
        
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'My Account - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/account', $data);
    }
    
    public function showFavorites()
    {
        $this->requireAuth();
        
        // Load favorites logic would go here
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'My Favorites - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/favorites', $data);
    }
}
