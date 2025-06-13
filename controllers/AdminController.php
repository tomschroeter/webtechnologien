<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/Database.php";

class AdminController extends BaseController
{
    private $db;
    private $customerRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->customerRepository = new CustomerLogonRepository($this->db);
    }
    
    public function manageUsers()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        // Check if user is admin
        if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
            $this->redirectWithMessage('/', 'Access denied. Administrator privileges required.', 'error');
            return;
        }
        
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'flashMessage' => $flashMessage,
            'title' => 'Manage Users - Admin Panel'
        ];
        
        echo $this->renderWithLayout('admin/manage-users', $data);
    }
}
