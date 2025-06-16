<?php

require_once __DIR__ . "/BaseController.php";
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

        // Handle POST actions for user management
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUserAction();
            return;
        }
        
        // Fetch users and admin count
        $users = $this->customerRepository->getAllUsersWithLogonData();
        $adminCount = $this->customerRepository->countActiveAdmins();
        
        // Handle error messages
        $error = $_GET['error'] ?? null;
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'users' => $users,
            'adminCount' => $adminCount,
            'error' => $error,
            'flashMessage' => $flashMessage,
            'title' => 'Manage Users - Admin Panel'
        ];
        
        echo $this->renderWithLayout('admin/manage-users', $data);
    }
    
    private function handleUserAction()
    {
        $customerID = (int)($_POST['customerId'] ?? 0);
        $action = $_POST['action'] ?? '';

        // Check if trying to demote the last admin
        if ($action === 'demote') {
            $adminCount = $this->customerRepository->countActiveAdmins();
            if ($adminCount <= 1) {
                $this->redirect('/manage-users?error=lastadmin');
                return;
            }
        }

        // Check if trying to deactivate the last active admin
        if ($action === 'deactivate') {
            $user = $this->customerRepository->getUserDetailsById($customerID);
            if ($user && $user['isAdmin']) {
                $adminCount = $this->customerRepository->countActiveAdmins();
                if ($adminCount <= 1) {
                    $this->redirect('/manage-users?error=lastadmin');
                    return;
                }
            }
        }

        if ($customerID && in_array($action, ['promote', 'demote', 'deactivate', 'activate'])) {
            if ($action === 'promote') {
                $this->customerRepository->updateUserAdmin($customerID, true);
            } elseif ($action === 'demote') {
                $this->customerRepository->updateUserAdmin($customerID, false);
                
                // Check if admin is demoting themselves
                if (isset($_SESSION['customerId']) && 
                    $customerID === (int)$_SESSION['customerId']) {
                    // Update session to reflect they're no longer admin
                    $_SESSION['isAdmin'] = false;
                    
                    // Redirect to home page instead of manage-users
                    $this->redirect('/');
                    return;
                }
            } elseif ($action === 'activate') {
                $this->customerRepository->updateUserState($customerID, 1);
            } elseif ($action === 'deactivate') {
                $this->customerRepository->updateUserState($customerID, 0);
            }

            $this->redirect('/manage-users');
        }
    }
}
