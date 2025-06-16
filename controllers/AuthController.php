<?php

require_once dirname(__DIR__) . "/controllers/BaseController.php";
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
        
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'favoriteArtists' => $favoriteArtists,
            'favoriteArtworks' => $favoriteArtworks,
            'flashMessage' => $flashMessage,
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
                $this->redirectWithMessage('/', 'Access denied. Administrator privileges required.', 'error');
                return;
            }
            $isAdminEdit = true;
            $userId = (int)$userId;
        } else {
            // User editing their own profile
            $userId = (int)$_SESSION['customerId'];
        }
        
        if (!$userId || !is_numeric($userId)) {
            $this->redirectWithMessage($isAdminEdit ? '/manage-users' : '/account', 'Invalid user ID.', 'error');
            return;
        }

        $user = $this->customerRepository->getUserDetailsById($userId);

        if (!$user) {
            $this->redirectWithMessage($isAdminEdit ? '/manage-users' : '/account', 'User not found.', 'error');
            return;
        }

        $error = $_GET['error'] ?? null;
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'user' => $user,
            'userId' => $userId,
            'isAdminEdit' => $isAdminEdit,
            'error' => $error,
            'flashMessage' => $flashMessage,
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
                $this->redirectWithMessage('/', 'Access denied. Administrator privileges required.', 'error');
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
            $this->redirectWithMessage($redirectUrl, 'Invalid user ID.', 'error');
            return;
        }

        $first = trim($_POST['firstName'] ?? '');
        $last = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $isAdmin = $isAdminEdit && isset($_POST['isAdmin']) && $_POST['isAdmin'] === '1';
        
        $errors = [];

        // Validate input
        if (empty($first)) {
            $errors[] = "First name is required.";
        }
        if (empty($last)) {
            $errors[] = "Last name is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }

        // Check if email is already taken by another user
        $existingUser = $this->customerRepository->getUserByEmail($email);
        if ($existingUser && $existingUser['CustomerID'] != $userId) {
            $errors[] = "This email address is already in use by another user.";
        }

        if (!empty($errors)) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId?error=validation" : "/edit-profile?error=validation";
            
            // Store errors in session for display
            $_SESSION['validation_errors'] = $errors;
            $this->redirect($redirectUrl);
            return;
        }

        try {
            $this->customerRepository->updateCustomerBasicInfo($userId, $first, $last, $email);
            
            // Only update admin status if this is an admin edit
            if ($isAdminEdit) {
                $this->customerRepository->updateUserAdmin($userId, $isAdmin);
                
                // Check if admin is demoting themselves
                if (isset($_SESSION['customerId']) && 
                    $userId === (int)$_SESSION['customerId'] && 
                    !$isAdmin && 
                    ($_SESSION['isAdmin'] ?? false)) {
                    
                    // Update session to reflect they're no longer admin
                    $_SESSION['isAdmin'] = false;
                    
                    // Redirect to home page instead of manage-users
                    $this->redirectWithMessage('/', 'You have been demoted from admin status.', 'info');
                    return;
                }
            }

            $successMessage = $isAdminEdit ? 'User updated successfully.' : 'Your profile has been updated successfully.';
            $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
            $this->redirectWithMessage($redirectUrl, $successMessage, 'success');
            
        } catch (Exception $e) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId?error=update" : "/edit-profile?error=update";
            
            $_SESSION['validation_errors'] = ["An error occurred while updating the profile. Please try again."];
            $this->redirect($redirectUrl);
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
            $this->redirectWithMessage('/account', 'User not found.', 'error');
            return;
        }

        // CSRF token generation
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $error = $_GET['error'] ?? null;
        $flashMessage = $this->getFlashMessage();
        
        $data = [
            'user' => $user,
            'csrf_token' => $_SESSION['csrf_token'],
            'error' => $error,
            'flashMessage' => $flashMessage,
            'title' => 'Change Password'
        ];
        
        echo $this->renderWithLayout('auth/change-password', $data);
    }
    
    public function updatePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        
        // CSRF token validation
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->redirectWithMessage('/account', 'Invalid security token. Please try again.', 'error');
            return;
        }
        
        $userId = (int)$_SESSION['customerId'];
        $user = $this->customerRepository->getUserDetailsById($userId);
        
        if (!$user) {
            $this->redirectWithMessage('/account', 'User not found.', 'error');
            return;
        }
        
        $oldPassword = $_POST['oldPassword'] ?? '';
        $newPassword1 = $_POST['newPassword1'] ?? '';
        $newPassword2 = $_POST['newPassword2'] ?? '';
        
        $errors = [];
        
        // Get current user logon data
        $userLogon = $this->customerRepository->getActiveUserByUsername($user['UserName']);
        
        // Validate old password
        if (!$userLogon || !password_verify($oldPassword, $userLogon['Pass'])) {
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
        if (empty($errors) && password_verify($newPassword1, $userLogon['Pass'])) {
            $errors[] = 'New password must be different from your current password.';
        }
        
        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $this->redirect('/change-password?error=validation');
            return;
        }
        
        try {
            $hashed = password_hash($newPassword1, PASSWORD_DEFAULT);
            $salt = substr($hashed, 7, 22);
            $this->customerRepository->updateCustomerPassword($userId, $hashed, $salt);
            
            $this->redirectWithMessage('/account', 'Password changed successfully.', 'success');
            
        } catch (Exception $e) {
            $_SESSION['validation_errors'] = ["An error occurred while updating your password. Please try again."];
            $this->redirect('/change-password?error=update');
        }
    }
}
