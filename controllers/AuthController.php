<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Customer.php";
require_once dirname(__DIR__) . "/classes/CustomerLogon.php";

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
        if (isset($_SESSION['customerId'])) {
            $this->redirect('/');
        }
        
        // Handle error and logout messages from URL params
        $error = $_GET['error'] ?? null;
        $logout = $_GET['logout'] ?? null;
        
        $data = [
            'error' => $error,
            'logout' => $logout,
            'title' => 'Login - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/login', $data);
    }
    
    public function processLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            $this->redirect('/login?error=missing');
        }

        $user = $this->customerRepository->getActiveUserByUsername($username);

        if (!$user || !password_verify($password, $user['Pass'])) {
            $this->redirect('/login?error=invalid');
        }

        $_SESSION['customerId'] = $user['CustomerID'];
        $_SESSION['username'] = $user['UserName'];
        $_SESSION['isAdmin'] = $user['isAdmin'] ?? false;

        $this->redirect('/?login=success');
    }
    
    
    public function showRegister()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If user is already logged in, redirect to home
        if (isset($_SESSION['customerId'])) {
            $this->redirect('/');
        }
        
        // Handle error messages from URL params
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;
        
        $data = [
            'error' => $error,
            'success' => $success,
            'formData' => [], // Empty form data on GET request
            'title' => 'Register - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/register', $data);
    }
    
    public function processRegister()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $postal = trim($_POST['postal'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
        $validPassword = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);

        if (!$lastName || !$city || !$address || !$country || !$email || !$password) {
            $this->redirect('/register?error=empty_field');
        } elseif (!$validPhoneNumber && !empty($phone)) {
            $this->redirect('/register?error=invalid_phone_number');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/register?error=invalid_email');
        } elseif (!$validPassword) {
            $this->redirect('/register?error=invalid_password');
        } elseif ($password !== $password2) {
            $this->redirect('/register?error=password_mismatch');
        } elseif ($this->customerRepository->userExists($username)) {
            $this->redirect('/register?error=exists');
        } else {
            try {
                $customer = new Customer($firstName, $lastName, $address, $city, $country, $postal, $email, $region, $phone);
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $logon = new CustomerLogon($username, $hashed, 1, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));
                
                // Use atomic registration method to prevent race conditions
                $customerId = $this->customerRepository->registerCustomer($customer, $logon);
                
                // Automatically log the user in after successful registration
                $_SESSION['customerId'] = $customerId;
                $_SESSION['username'] = $username;
                $_SESSION['isAdmin'] = false; // New users are always regular users
                
                // Redirect to home page (logged in)
                $this->redirect('/?welcome=1');
            } catch (Exception $e) {
                $this->redirect('/register?error=database');
            }
        }
    }
    
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        $this->redirect('/login?logout=1');
    }
    
    public function showAccount()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['customerId'])) {
            $this->redirect('/login');
        }
        
        $id = (int)$_SESSION['customerId'];
        $user = $this->customerRepository->getUserDetailsById($id);
        $customer = $this->customerRepository->getCustomerById($id);

        if (!$user || !$customer) {
            $this->redirect('/error.php?error=userNotFound');
        }
        
        // Handle success messages
        $success = $_GET['success'] ?? null;
        
        $data = [
            'user' => $user,
            'customer' => $customer,
            'success' => $success,
            'title' => 'My Account - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/account', $data);
    }
    
    public function showFavorites()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['customerId'])) {
            $this->redirect('/login');
        }
        
        // Load favorites logic would go here
        // For now, we'll just render the favorites view
        
        $data = [
            'title' => 'My Favorites - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/favorites', $data);
    }
}
