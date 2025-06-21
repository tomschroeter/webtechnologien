<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Customer.php";
require_once dirname(__DIR__) . "/classes/CustomerLogon.php";

class AuthController extends BaseController
{
    private $db;
    private $customerRepository;
    private $artistRepository;
    private $artworkRepository;
    
    public function __construct()
    {
        $this->db = new Database();
        $this->customerRepository = new CustomerLogonRepository($this->db);
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
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

        // Get form data from session if available, then clear it
        $formData = $_SESSION['login_form_data'] ?? [];
        unset($_SESSION['login_form_data']);
        
        $data = [
            'title' => 'Login - Art Gallery',
            'formData' => $formData,
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

        // Store form data in session for repopulating form on validation errors
        // Don't store passwords for security reasons
        $_SESSION['login_form_data'] = [
            'username' => $username
        ];

        if (!$username || !$password) {
            $this->redirectWithNotification(
                '/login',
                'Username or password is missing.',
                'error',
            );
        }

        $user = $this->customerRepository->getActiveUserByUsername($username);

        if (!$user || !password_verify($password, $user->getPass())) {
            $this->redirectWithNotification(
                '/login',
                'Password is incorrect.',
                'error',
            );
        }

        $_SESSION['customerId'] = $user->getCustomerId();
        $_SESSION['username'] = $user->getUserName();
        $_SESSION['isAdmin'] = $user->getIsAdmin();

        $this->redirectWithNotification(
            '/',
            'Welcome back, ' . htmlspecialchars($user->getUserName()) . '! You have successfully logged in.',
            'success',
        );
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
        
        // Get form data from session if available, then clear it
        $formData = $_SESSION['register_form_data'] ?? [];
        unset($_SESSION['register_form_data']);
        
        $data = [
            'title' => 'Register - Art Gallery',
            'formData' => $formData
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

        // Store form data in session for repopulating form on validation errors
        // Don't store passwords for security reasons
        $_SESSION['register_form_data'] = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => $address,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'postal' => $postal,
            'phone' => $phone,
            'email' => $email,
            'username' => $username
        ];

        $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
        $validPassword = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);

        if (!$lastName || !$city || !$address || !$country || !$email || !$password) {
            $this->redirectWithNotification(
                '/register',
                'Please fill out all required fields.',
                'error',
            );
        } elseif (!$validPhoneNumber && !empty($phone)) {
            $this->redirectWithNotification(
                '/register',
                'The phone number does not have a valid format.',
                'error',
            );
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithNotification(
                '/register',
                'The E-Mail does not have a valid format.',
                'error',
            );
        } elseif (!$validPassword) {
            $this->redirectWithNotification(
                '/register',
                'The password must contain at least 6 characters, one uppercase letter, one number, and one special character.',
                'error',
            );
        } elseif ($password !== $password2) {
            $this->redirectWithNotification(
                '/register',
                'Passwords do not match. Please try again.',
                'error',
            );
        } elseif ($this->customerRepository->userExists($username)) {
            $this->redirectWithNotification(
                '/register',
                'Username already exists. Please choose another one.',
                'error',
            );
        } else {
            try {
                $customer = new Customer(
                    null,
                    $firstName,
                    $lastName,
                    $address,
                    $city,
                    $region,
                    $country,
                    $postal,
                    $phone,
                    $email
                );
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $logon = new CustomerLogon(
                    null,
                    $username,
                    $hashed,
                    1,
                    0,
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                    0
                );
                
                // Use atomic registration method to prevent race conditions
                $customerId = $this->customerRepository->registerCustomer($customer, $logon);
                
                // Clear form data from session on successful registration
                unset($_SESSION['register_form_data']);
                
                // Automatically log the user in after successful registration
                $_SESSION['customerId'] = $customerId;
                $_SESSION['username'] = $username;
                $_SESSION['isAdmin'] = false; // New users are always regular users
                
                // Redirect to home page (logged in)
                $this->redirectWithNotification(
                    '/',
                    'Welcome to Art Gallery, ' . htmlspecialchars($username) . '! Your account has been created successfully.',
                    'success',
                );
            } catch (Exception $e) {
                $this->redirectWithNotification(
                    '/register',
                    'Registration failed due to a database error. Please try again.',
                    'error',
                );
            }
        }
    }
    
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();

        $this->redirectWithNotification(
            '/login',
            'You have been successfully logged out.',
            'success',
        );
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
            throw new HttpException(404, "User not found.");
        }
        
        $data = [
            'user' => $user,
            'customer' => $customer,
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
        
        // Get favorite artists and artworks from session
        $favoriteArtistIds = $_SESSION['favoriteArtists'] ?? [];
        $favoriteArtworkIds = $_SESSION['favoriteArtworks'] ?? [];
        
        $favoriteArtists = [];
        $favoriteArtworks = [];
        $cleanupNeeded = false;
        
        // Fetch favorite artists
        if (!empty($favoriteArtistIds)) {
            foreach ($favoriteArtistIds as $artistId) {
                try {
                    $artist = $this->artistRepository->getArtistById($artistId);
                    if ($artist) {
                        $favoriteArtists[] = $artist;
                    } else {
                        // Remove invalid artist from session
                        if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                            unset($_SESSION['favoriteArtists'][$key]);
                            $cleanupNeeded = true;
                        }
                    }
                } catch (Exception $e) {
                    // Remove invalid artist from session
                    if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                        unset($_SESSION['favoriteArtists'][$key]);
                        $cleanupNeeded = true;
                    }
                }
            }
        }
        
        // Fetch favorite artworks  
        if (!empty($favoriteArtworkIds)) {
            foreach ($favoriteArtworkIds as $artworkId) {
                try {
                    $artwork = $this->artworkRepository->findById($artworkId);
                    if ($artwork) {
                        // Get artist information for the artwork
                        try {
                            $artist = $this->artistRepository->getArtistById($artwork->getArtistId());
                            $favoriteArtworks[] = ['artwork' => $artwork, 'artist' => $artist];
                        } catch (Exception $e) {
                            // If artist not found, still include artwork without artist info
                            $favoriteArtworks[] = ['artwork' => $artwork, 'artist' => null];
                        }
                    } else {
                        // Remove invalid artwork from session
                        if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                            unset($_SESSION['favoriteArtworks'][$key]);
                            $cleanupNeeded = true;
                        }
                    }
                } catch (Exception $e) {
                    // Remove invalid artwork from session
                    if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                        unset($_SESSION['favoriteArtworks'][$key]);
                        $cleanupNeeded = true;
                    }
                }
            }
        }
        
        // Re-index arrays if cleanup was needed
        if ($cleanupNeeded) {
            $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists'] ?? []);
            $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks'] ?? []);
        }
        
        $data = [
            'favoriteArtists' => $favoriteArtists,
            'favoriteArtworks' => $favoriteArtworks,
            'title' => 'My Favorites - Art Gallery'
        ];
        
        echo $this->renderWithLayout('auth/favorites', $data);
    }
    
    public function toggleArtistFavoriteAjax($artistId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
        }
        
        $artistId = (int)$artistId;
        if (!$artistId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid artist ID'], 400);
        }
        
        try {
            if (!isset($_SESSION['favoriteArtists'])) {
                $_SESSION['favoriteArtists'] = [];
            }
            
            $isFavorite = in_array($artistId, $_SESSION['favoriteArtists']);
            
            if ($isFavorite) {
                // Remove from favorites
                if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                    unset($_SESSION['favoriteArtists'][$key]);
                    $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists']);
                }
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artist removed from favorites!',
                    'isFavorite' => false,
                    'action' => 'removed'
                ]);
            } else {
                // Add to favorites
                $_SESSION['favoriteArtists'][] = $artistId;
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artist added to favorites!',
                    'isFavorite' => true,
                    'action' => 'added'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error updating favorites'], 500);
        }
    }
    
    public function toggleArtworkFavoriteAjax($artworkId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
        }
        
        $artworkId = (int)$artworkId;
        if (!$artworkId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid artwork ID'], 400);
        }
        
        try {
            if (!isset($_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'] = [];
            }
            
            $isFavorite = in_array($artworkId, $_SESSION['favoriteArtworks']);
            
            if ($isFavorite) {
                // Remove from favorites
                if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                    unset($_SESSION['favoriteArtworks'][$key]);
                    $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks']);
                }
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artwork removed from favorites!',
                    'isFavorite' => false,
                    'action' => 'removed'
                ]);
            } else {
                // Add to favorites
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artwork added to favorites!',
                    'isFavorite' => true,
                    'action' => 'added'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error updating favorites'], 500);
        }
    }
    
    public function editProfile($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        // Get user ID - either from path parameter (admin editing) or session (user editing own profile)
        $userId = $id ?? $_GET['id'] ?? null;
        $isAdminEdit = false;
        
        if ($userId) {
            // Admin editing another user's profile
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                $this->redirectWithNotification('/', 'Access denied. Administrator privileges required.', 'error');
                return;
            }
            $isAdminEdit = true;
            $userId = (int)$userId;
        } else {
            // User editing their own profile
            $userId = (int)$_SESSION['customerId'];
        }
        
        if (!$userId || !is_numeric($userId)) {
            $this->redirectWithNotification($isAdminEdit ? '/manage-users' : '/account', 'Invalid user ID.', 'error');
            return;
        }

        $user = $this->customerRepository->getUserDetailsById($userId);

        if (!$user) {
            $this->redirectWithNotification($isAdminEdit ? '/manage-users' : '/account', 'User not found.', 'error');
            return;
        }

        $data = [
            'user' => $user,
            'userId' => $userId,
            'isAdminEdit' => $isAdminEdit,
            'title' => $isAdminEdit ? 'Edit User - Admin Panel' : 'Edit Profile'
        ];
        
        echo $this->renderWithLayout('auth/edit-profile', $data);
    }
    
    public function updateProfile($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        // Get user ID and determine if this is an admin edit
        $userId = $id ?? $_POST['userId'] ?? null;
        $isAdminEdit = false;
        
        if ($userId && (int)$userId !== (int)$_SESSION['customerId']) {
            // Admin editing another user's profile
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                $this->redirectWithNotification('/', 'Access denied. Administrator privileges required.', 'error');
                return;
            }
            $isAdminEdit = true;
            $userId = (int)$userId;
        } else {
            // User editing their own profile
            $userId = (int)$_SESSION['customerId'];
        }

        if (!$userId || !is_numeric($userId)) {
            $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
            $this->redirectWithNotification($redirectUrl, 'Invalid user ID.', 'error');
            return;
        }

        $first = trim($_POST['firstName'] ?? '');
        $last = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $postal = trim($_POST['postal'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        $errors = [];

        // Validate input
        if (empty($last)) {
            $errors[] = "Last name is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
        if (empty($address)) {
            $errors[] = "Address is required.";
        }
        if (empty($city)) {
            $errors[] = "City is required.";
        }
        if (empty($country)) {
            $errors[] = "Country is required.";
        }
        $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
        if (!empty($phone) && !$validPhoneNumber) {
            $errors[] = "Please enter a valid phone number.";
        }

        // Check if email is already taken by another user
        $existingUser = $this->customerRepository->getUserDetailsByEmail($email);
        if ($existingUser && $existingUser->getCustomerId() != $userId) {
            $errors[] = "This email address is already in use by another user.";
        }

        if (!empty($errors)) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            
            // Create notifications array from errors
            $notifications = [];
            foreach ($errors as $error) {
                $notifications[] = ['message' => $error, 'type' => 'error'];
            }
            
            $this->redirectWithNotifications($redirectUrl, $notifications);
            return;
        }

        try {
            $this->customerRepository->updateCustomerFullInfo(
                $userId,
                $first,
                $last,
                $address,
                $city,
                $region,
                $country,
                $postal,
                $phone,
                $email
            );
            
            $successMessage = $isAdminEdit ? 'User updated successfully.' : 'Your profile has been updated successfully.';
            $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
            $this->redirectWithNotification($redirectUrl, $successMessage, 'success');
            
        } catch (Exception $e) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            
            $this->redirectWithNotification($redirectUrl, 'An error occurred while updating the profile. Please try again.', 'error');
        }
    }
    
    public function changePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        $userId = (int)$_SESSION['customerId'];
        $user = $this->customerRepository->getUserDetailsById($userId);

        if (!$user) {
            $this->redirectWithNotification('/account', 'User not found.', 'error');
            return;
        }

        $data = [
            'user' => $user,
            'title' => 'Change Password'
        ];
        
        echo $this->renderWithLayout('auth/change-password', $data);
    }
    
    public function updatePassword($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        // Get user ID - either from path parameter (admin editing) or session (user editing own profile)
        $userId = $id ?? $_GET['id'] ?? null;
        $isAdminEdit = false;
        
        if ($userId) {
            // Admin editing another user's profile
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                $this->redirectWithNotification('/', 'Access denied. Administrator privileges required.', 'error');
                return;
            }
            $isAdminEdit = true;
            $userId = (int)$userId;
        } else {
            // User editing their own profile
            $userId = (int)$_SESSION['customerId'];
        }
        echo $userId;
        $user = $this->customerRepository->getUserDetailsById($userId);
        
        if (!$user) {
            $this->redirectWithNotification('/account', 'User not found.', 'error');
            return;
        }
        
        $oldPassword = $_POST['oldPassword'] ?? '';
        $newPassword1 = $_POST['newPassword1'] ?? '';
        $newPassword2 = $_POST['newPassword2'] ?? '';
        
        $errors = [];
        
        // Get current user logon data
        $userLogon = $this->customerRepository->getActiveUserByUsername($user->getUserName());
        
        // Validate old password
        if (!$isAdminEdit && (!$userLogon || !password_verify($oldPassword, $userLogon->getPass()))) {
            $errors[] = 'Current password is incorrect.';
        }
        
        // Validate new passwords match
        if ($newPassword1 !== $newPassword2) {
            $errors[] = 'New passwords do not match.';
        }
        
        // Validate password strength
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $newPassword1)) {
            $errors[] = 'New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.';
        }
        
        // Check if new password is same as old
        if (empty($errors) && password_verify($newPassword1, $userLogon->getPass())) {
            $errors[] = 'New password must be different from your current password.';
        }
        
        if (!empty($errors)) {
            $notifications = [];
            foreach ($errors as $error) {
                $notifications[] = ['message' => $error, 'type' => 'error'];
            }

            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            $this->redirectWithNotifications($redirectUrl, $notifications);
            return;
        }
        
        try {
            $hashed = password_hash($newPassword1, PASSWORD_DEFAULT);
            $this->customerRepository->updateCustomerPassword($userId, $hashed);
            
            $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
            $this->redirectWithNotification($redirectUrl, 'Password changed successfully.', 'success');
            
        } catch (Exception $e) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            $this->redirectWithNotification($redirectUrl, 'An error occurred while updating the password. Please try again.', 'error');
        }
    }
}
