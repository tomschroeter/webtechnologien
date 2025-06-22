<?php

require_once __DIR__ . "/BaseController.php";
require_once dirname(__DIR__) . "/repositories/CustomerLogonRepository.php";
require_once dirname(__DIR__) . "/Database.php";

/**
 * Handles administrative functions such as managing users,
 * updating user roles (admin/user), and activating or deactivating accounts.
 */
class AdminController extends BaseController
{
    private Database $db;
    private CustomerLogonRepository $customerRepository;

    /**
     * Initializes the database and customer repository.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->customerRepository = new CustomerLogonRepository($this->db);
    }

    /**
     * Displays the user management panel to an authenticated admin
     * 
     * Fetches all user data and count of active administrators
     * Only accessible to users with admin privileges
     * Redirects unauthorized users with an error notification
     */
    public function manageUsers(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->requireAuth();

        // Check if user is admin
        if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
            throw new HttpException(403, 'Access denied. Administrator privileges required.');
        }

        // Fetch users and admin count
        $users = $this->customerRepository->getAllCustomersWithLogonData();
        $adminCount = $this->customerRepository->countActiveAdmins();

        $data = [
            'users' => $users,
            'adminCount' => $adminCount,
            'title' => 'Manage Users - Admin Panel'
        ];

        echo $this->renderWithLayout('admin/manage-users', $data);
    }

    /**
     * Handles user actions (promote, demote, activate, deactivate)
     * 
     * Validates session and user permissions, protects against demoting or
     * deactivating the last admin, and updates user state accordingly
     * Redirects with success or error notification.
     */
    public function handleUserAction(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->requireAuth();

        // Ensure user is an admin
        if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
            throw new HttpException(403, 'Access denied. Administrator privileges required.');
        }

        $customerID = ($_POST['customerId'] ?? 0);

        // Validate the customer ID
        if (!$customerID || !is_numeric($customerID)) {
            throw new HttpException(400, "The customer ID parameter is invalid or missing.");
        }

        $customerID = (int) $customerID;

        $action = $_POST['action'] ?? '';


        try {
            $user = $this->customerRepository->getCustomerDetailsById($customerID);
        }
        catch (CustomerNotFoundException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        // Prevent demotion of the last admin
        if ($action === 'demote') {
            $adminCount = $this->customerRepository->countActiveAdmins();
            if ($adminCount <= 1) {
                $this->redirectWithNotification(
                    '/manage-users',
                    'You can not demote yourself because you are the last admin.',
                    'error'
                );
            }
        }

        // Prevent deactivation of the last admin
        if ($action === 'deactivate') {

            if ($user && $user->getIsAdmin()) {
                $adminCount = $this->customerRepository->countActiveAdmins();
                if ($adminCount <= 1) {
                    $this->redirectWithNotification(
                        '/manage-users',
                        'You can not deactivate yourself because you are the last admin.',
                        'error'
                    );
                }
            }
        }

        // Perform the requested action
        if ($customerID && in_array($action, ['promote', 'demote', 'deactivate', 'activate'])) {
            if ($action === 'promote') {
                $this->customerRepository->updateUserAdmin($customerID, true);
            } elseif ($action === 'demote') {
                $this->customerRepository->updateUserAdmin($customerID, false);

                // Check if admin is demoting themselves
                if (isset($_SESSION['customerId']) && $customerID === (int) $_SESSION['customerId']) {
                    $_SESSION['isAdmin'] = false;
                    $this->redirect('/');
                    return;
                }
            } elseif ($action === 'activate') {
                $this->customerRepository->updateCustomerState($customerID, 1);
            } elseif ($action === 'deactivate') {
                $this->customerRepository->updateCustomerState($customerID, 0);
            }

            $this->redirectWithNotification(
                '/manage-users',
                'Successfully updated!',
                'success'
            );
        }
    }
}
