<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtistRepository.php";
require_once dirname(__DIR__) . "/repositories/ArtworkRepository.php";
require_once dirname(__DIR__) . "/Database.php";
require_once dirname(__DIR__) . "/classes/Customer.php";
require_once dirname(__DIR__) . "/classes/CustomerLogon.php";
require_once dirname(__DIR__) . "/exceptions/HttpException.php";
require_once dirname(__DIR__) . "/exceptions/ArtworkNotFound.php";

/**
 * Controller handling user authentication (login, registration, logout)
 */
class AuthController extends BaseController
{
    private Database $db;
    private CustomerLogonRepository $customerRepository;
    private ArtistRepository $artistRepository;
    private ArtworkRepository $artworkRepository;

    /**
     * Initializes the database and repositories.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->customerRepository = new CustomerLogonRepository($this->db);
        $this->artistRepository = new ArtistRepository($this->db);
        $this->artworkRepository = new ArtworkRepository($this->db);
    }

    /**
     * Display login form, or redirect if already logged in
     */
    public function showLogin(): void
    {
        if ($this->getCurrentUser()) {
            $this->redirect('/');
        }

        $formData = $_SESSION['login_form_data'] ?? [];
        unset($_SESSION['login_form_data']);

        $data = [
            'title' => 'Login - Art Gallery',
            'formData' => $formData,
        ];

        echo $this->renderWithLayout('auth/login', $data);
    }

    /**
     * Handle login form submission
     */
    public function processLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $_SESSION['login_form_data'] = ['username' => $username];

        // Validate form input
        if (!$username || !$password) {
            $this->redirectWithNotification('/login', 'Username or password is missing.', 'error');
        }

        // Authenticate user
        $user = $this->customerRepository->getActiveCustomerByUsername($username);

        if (!$user || !password_verify($password, $user->getPass())) {
            $this->redirectWithNotification('/login', 'Password is incorrect.', 'error');
        }

        // Set session variables
        $_SESSION['customerId'] = $user->getCustomerId();
        $_SESSION['username'] = $user->getUserName();
        $_SESSION['isAdmin'] = $user->getIsAdmin();

        // Redirect to home
        $this->redirectWithNotification('/', 'Welcome back, ' . htmlspecialchars($user->getUserName()) . '! You have successfully logged in.', 'success');
    }

    /**
     * Display registration form
     */
    public function showRegister(): void
    {
        if ($this->getCurrentUser()) {
            $this->redirect('/');
        }

        $formData = $_SESSION['register_form_data'] ?? [];
        unset($_SESSION['register_form_data']);

        $data = [
            'title' => 'Register - Art Gallery',
            'formData' => $formData
        ];

        echo $this->renderWithLayout('auth/register', $data);
    }

    /**
     * Handle registration form submission
     */
    public function processRegister(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Extract and sanitize input
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

        // Store safe form data for repopulation on errors
        $_SESSION['register_form_data'] = compact('firstName', 'lastName', 'address', 'city', 'region', 'country', 'postal', 'phone', 'email', 'username');

        // Validate input
        $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
        $validPassword = preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password);

        if (!$lastName || !$city || !$address || !$country || !$email || !$password) {
            $this->redirectWithNotification('/register', 'Please fill out all required fields.', 'error');
        } elseif (!$validPhoneNumber && !empty($phone)) {
            $this->redirectWithNotification('/register', 'The phone number does not have a valid format.', 'error');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) { // FILTER_FLAG_EMAIL_UNICODE to allow characters like ł or ó
            $this->redirectWithNotification('/register', 'The E-Mail does not have a valid format.', 'error');
        } elseif (!$validPassword) {
            $this->redirectWithNotification('/register', 'The password must contain at least 6 characters, one uppercase letter, one number, and one special character.', 'error');
        } elseif ($password !== $password2) {
            $this->redirectWithNotification('/register', 'Passwords do not match. Please try again.', 'error');
        } elseif ($this->customerRepository->customerExists($username)) {
            $this->redirectWithNotification('/register', 'Username already exists. Please choose another one.', 'error');
        } elseif ($this->customerRepository->getCustomerDetailsByEmail($email)) {
            $this->redirectWithNotification('/register', 'This email address is already in use by another user.', 'error');
        }

        // Register user
        $customer = new Customer(null, $firstName, $lastName, $address, $city, $region, $country, $postal, $phone, $email);
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $logon = new CustomerLogon(null, $username, $hashed, 1, 0, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), 0);

        $customerId = $this->customerRepository->registerCustomer($customer, $logon);

        unset($_SESSION['register_form_data']);

        // Log user in immediately
        $_SESSION['customerId'] = $customerId;
        $_SESSION['username'] = $username;
        $_SESSION['isAdmin'] = false;

        $this->redirectWithNotification('/', 'Welcome to Art Gallery, ' . htmlspecialchars($username) . '! Your account has been created successfully.', 'success');
    }

    /**
     * Log the user out and destroy session
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();

        $this->redirectWithNotification('/login', 'You have been successfully logged out.', 'success');
    }

    /**
     * Display current user's account details
     */
    public function showAccount(): void
    {
        $this->requireAuth();

        $id = (int) $_SESSION['customerId'];

        $user = $this->customerRepository->getCustomerDetailsById($id);
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

    /**
     * Display user's favorites
     */
    public function showFavorites(): void
    {
        $this->requireAuth();

        // Retrieve favorite artist and artwork IDs from session
        $favoriteArtistIds = $_SESSION['favoriteArtists'] ?? [];
        $favoriteArtworkIds = $_SESSION['favoriteArtworks'] ?? [];

        $favoriteArtists = [];
        $favoriteArtworks = [];
        $cleanupNeeded = false;

        // Load favorite artists from repository
        foreach ($favoriteArtistIds as $artistId) {
            try {
                $artist = $this->artistRepository->getArtistById($artistId);
                if ($artist) {
                    $favoriteArtists[] = $artist;
                } else {
                    // Remove invalid artist ID from session
                    if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                        unset($_SESSION['favoriteArtists'][$key]);
                        $cleanupNeeded = true;
                    }
                }
            } catch (Exception $e) {
                // Remove if artist lookup failed
                if (($key = array_search($artistId, $_SESSION['favoriteArtists'])) !== false) {
                    unset($_SESSION['favoriteArtists'][$key]);
                    $cleanupNeeded = true;
                }
            }
        }

        // Load favorite artworks from repository
        foreach ($favoriteArtworkIds as $artworkId) {
            try {
                $artwork = $this->artworkRepository->getArtworkById($artworkId);
                if ($artwork) {
                    try {
                        $artist = $this->artistRepository->getArtistById($artwork->getArtistId());
                        $favoriteArtworks[] = ['artwork' => $artwork, 'artist' => $artist];
                    } catch (Exception $e) {
                        // If artist not found, include artwork without artist info
                        $favoriteArtworks[] = ['artwork' => $artwork, 'artist' => null];
                    }
                } else {
                    // Remove invalid artwork ID from session
                    if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                        unset($_SESSION['favoriteArtworks'][$key]);
                        $cleanupNeeded = true;
                    }
                }
            } catch (Exception $e) {
                // Remove if artwork lookup failed
                if (($key = array_search($artworkId, $_SESSION['favoriteArtworks'])) !== false) {
                    unset($_SESSION['favoriteArtworks'][$key]);
                    $cleanupNeeded = true;
                }
            }
        }

        // Re-index arrays if cleanup occurred
        if ($cleanupNeeded) {
            $_SESSION['favoriteArtists'] = array_values($_SESSION['favoriteArtists'] ?? []);
            $_SESSION['favoriteArtworks'] = array_values($_SESSION['favoriteArtworks'] ?? []);
        }

        // Render favorites view with collected data
        $data = [
            'favoriteArtists' => $favoriteArtists,
            'favoriteArtworks' => $favoriteArtworks,
            'title' => 'My Favorites - Art Gallery'
        ];

        echo $this->renderWithLayout('auth/favorites', $data);
    }

    /**
     * Add or remove artist from favorites
     * 
     * @param int|string $artistId The ID of the artist
     * 
     */
    public function toggleArtistFavoriteAjax($artistId): void
    {
        $this->requireAuth();

        if (!$artistId || !is_numeric($artistId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid artist ID'], 400);
        }

        try {
            $this->artistRepository->getArtistById($artistId);
        }
        catch (ArtistNotFoundException $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }

        $artistId = (int) $artistId;

        try {
            // Initialize session array if not already set
            if (!isset($_SESSION['favoriteArtists'])) {
                $_SESSION['favoriteArtists'] = [];
            }

            $isFavorite = in_array($artistId, $_SESSION['favoriteArtists']);

            if ($isFavorite) {
                // Remove artist from favorites
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
                // Add artist to favorites
                $_SESSION['favoriteArtists'][] = $artistId;
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artist added to favorites!',
                    'isFavorite' => true,
                    'action' => 'added'
                ]);
            }
        // Exception needs to be catched here, to return JSON response instead of default error page
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error updating favorites'], 500);
        }
    }

    /**
     * Add or remove artwork from favorites
     * 
     * @param int|string $artworkId The ID of the artwork
     * 
     */
    public function toggleArtworkFavoriteAjax($artworkId): void
    {
        $this->requireAuth();

        if (!$artworkId || !is_numeric($artworkId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid artwork ID'], 400);
        }

        try {
            $this->artworkRepository->getArtworkById($artworkId);
        }
        catch (ArtworkNotFoundException $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }

        $artworkId = (int) $artworkId;

        try {
            // Initialize session array if not already set
            if (!isset($_SESSION['favoriteArtworks'])) {
                $_SESSION['favoriteArtworks'] = [];
            }

            $isFavorite = in_array($artworkId, $_SESSION['favoriteArtworks']);

            if ($isFavorite) {
                // Remove artwork from favorites
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
                // Add artwork to favorites
                $_SESSION['favoriteArtworks'][] = $artworkId;
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Artwork added to favorites!',
                    'isFavorite' => true,
                    'action' => 'added',
                ]);
            }
        // Exception needs to be catched here, to return JSON response instead of default error page
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error updating favorites'], 500);
        }
    }

    /**
     * Show form for editing user profile
     * 
     * @param int|string $id The ID of the user
     */
    public function editProfile($id = null): void
    {
        // Ensure user is authenticated
        $this->requireAuth();

        // Determine if editing own profile or another user's (admin)
        $userId = $id ?? $_GET['id'] ?? null;
        $isAdminEdit = false;

        // If admin is editing someone else's profile
        if ($userId) {
            // Prevent non-admins from editing other profiles
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                // $this->redirectWithNotification('/', 'Access denied. Administrator privileges required.', 'error');
                throw new HttpException(403, 'Access denied. Administrator privileges required.');
            }
            $isAdminEdit = true;
            $userId = (int) $userId;
        } else {
            // Regular user editing their own profile
            $userId = (int) $_SESSION['customerId'];
        }

        if (!$userId || !is_numeric($userId)) {
            throw new HttpException(400, 'Invalid user ID.');
        }

        // Retrieve user details
        try {
            $user = $this->customerRepository->getCustomerDetailsById($userId);
        }
        catch (CustomerNotFoundException $e) {
            throw new HttpException(404, $e->getMessage());
        }

        // Render the edit profile form
        $data = [
            'user' => $user,
            'userId' => $userId,
            'isAdminEdit' => $isAdminEdit,
            'title' => $isAdminEdit ? 'Edit User - Admin Panel' : 'Edit Profile'
        ];

        echo $this->renderWithLayout('auth/edit-profile', $data);
    }

    /**
     * Update user profile.
     * 
     * @param int|string $id The ID of the user
     * 
     */
    public function updateProfile($id = null): void
    {
        // Ensure the user is authenticated
        $this->requireAuth();

        // Determine the user ID: either passed explicitly or from POST/session
        $userId = $id ?? $_POST['userId'] ?? null;
        $isAdminEdit = false;

        // If editing another user's profile, check admin privileges
        if ($userId && (int) $userId !== (int) $_SESSION['customerId']) {
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                throw new HttpException(403, 'Access denied. Administrator privileges required.');
            }
            $isAdminEdit = true;
            $userId = (int) $userId;
        } else {
            // User is editing their own profile
            $userId = (int) $_SESSION['customerId'];
        }

        // Validate user ID
        if (!$userId || !is_numeric($userId)) {
            throw new HttpException(400, "The user ID parameter is invalid or missing.");
        }

        // Retrieve and sanitize form input
        $userName = trim($_POST['userName'] ?? '');
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

        // Validate required fields
        if (empty($userName))
            $errors[] = "Username is required.";
        else if ($this->customerRepository->customerExists($userName))
            $errors[] = "This username is already taken.";
        if (empty($last))
            $errors[] = "Last name is required.";
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) { // FILTER_FLAG_EMAIL_UNICODE to allow characters like ł or ó
            $errors[] = "Please enter a valid email address.";
        }
        if (empty($address))
            $errors[] = "Address is required.";
        if (empty($city))
            $errors[] = "City is required.";
        if (empty($country))
            $errors[] = "Country is required.";

        // Validate phone number format if provided
        $validPhoneNumber = preg_match('/^\+?[0-9\s\-\(\)\.\/xXextEXT\*#]{5,30}$/', $phone);
        if (!empty($phone) && !$validPhoneNumber) {
            $errors[] = "Please enter a valid phone number.";
        }

        // Check for email uniqueness (excluding self)
        $existingUser = $this->customerRepository->getCustomerDetailsByEmail($email);
        if ($existingUser && $existingUser->getCustomerId() != $userId) {
            $errors[] = "This email address is already in use by another user.";
        }

        // Handle validation errors
        if (!empty($errors)) {
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            $notifications = [];
            foreach ($errors as $error) {
                $notifications[] = ['message' => $error, 'type' => 'error'];
            }
            $this->redirectWithNotifications($redirectUrl, $notifications);
            return;
        }

        try {
            // Update user data in database
            $this->customerRepository->updateCustomerFullInfo(
                $userId,
                $userName,
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

            // Notify success and redirect accordingly
            $successMessage = $isAdminEdit ? 'User updated successfully.' : 'Your profile has been updated successfully.';
            $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
            $this->redirectWithNotification($redirectUrl, $successMessage, 'success');

        } catch (Exception $e) {
            // Handle unexpected error
            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            $this->redirectWithNotification($redirectUrl, 'An error occurred while updating the profile. Please try again.', 'error');
        }
    }

    /**
     * Display form for changing user password
     */
    public function changePassword(): void
    {
        // Ensure user is logged in
        $this->requireAuth();

        // Get user details from session
        $userId = (int) $_SESSION['customerId'];

        // Retrieve user details
        try {
            $user = $this->customerRepository->getCustomerDetailsById($userId);
        }
        catch (CustomerNotFoundException $e) {
            // In this case, it is a "forbidden" error, because not logged in users should not be able to use this page
            throw new HttpException(401, "Invalid session.");
        }

        // Render password change form
        $data = [
            'user' => $user,
            'title' => 'Change Password'
        ];

        echo $this->renderWithLayout('auth/change-password', $data);
    }

    public function updatePassword($id = null)
    {
        // Ensure user is authenticated
        $this->requireAuth();

        // Get user ID - either from path parameter (admin editing) or session (user editing own profile)
        $userId = $id ?? $_GET['id'] ?? null;
        $isAdminEdit = false;

        if ($userId) {
            // Admin editing another user's profile
            if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
                throw new HttpException(401, "Access denied. Administrator privileges required.");
            }
            $isAdminEdit = true;
            $userId = (int) $userId;
        } else {
            // User editing their own profile
            $userId = (int) $_SESSION['customerId'];
        }

        // Retrieve user details
        try {
            $user = $this->customerRepository->getCustomerDetailsById($userId);
        }
        catch (CustomerNotFoundException $e) {
            throw new HttpException(401, "Invalid session.");
        }

        // Collect submitted password fields
        $oldPassword = $_POST['oldPassword'] ?? '';
        $newPassword1 = $_POST['newPassword1'] ?? '';
        $newPassword2 = $_POST['newPassword2'] ?? '';

        $errors = [];

        // Get current password hash
        $userLogon = $this->customerRepository->getActiveCustomerByUsername($user->getUserName());

        // Validate old password
        if (!$isAdminEdit && (!$userLogon || !password_verify($oldPassword, $userLogon->getPass()))) {
            $errors[] = 'Current password is incorrect.';
        }

        // Ensure new passwords match
        if ($newPassword1 !== $newPassword2) {
            $errors[] = 'New passwords do not match.';
        }

        // Enforce password complexity requirements
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $newPassword1)) {
            $errors[] = 'New password must be at least 6 characters and contain an uppercase letter, a digit, and a special character.';
        }

        // Ensure new password is different
        if (empty($errors) && password_verify($newPassword1, $userLogon->getPass())) {
            $errors[] = 'New password must be different from your current password.';
        }

        // Handle validation errors
        if (!empty($errors)) {
            $notifications = [];
            foreach ($errors as $error) {
                $notifications[] = ['message' => $error, 'type' => 'error'];
            }

            $redirectUrl = $isAdminEdit ? "/edit-profile/$userId" : "/edit-profile";
            $this->redirectWithNotifications($redirectUrl, $notifications);
            return;
        }

        // Hash and update new password
        $hashed = password_hash($newPassword1, PASSWORD_DEFAULT);
        $this->customerRepository->updateCustomerPassword($userId, $hashed);

        $redirectUrl = $isAdminEdit ? '/manage-users' : '/account';
        $this->redirectWithNotification($redirectUrl, 'Password changed successfully.', 'success');
    }
}
