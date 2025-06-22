<?php

require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/dtos/CustomerWithLogonData.php";
require_once dirname(__DIR__) . "/exceptions/CustomerNotFound.php";

class CustomerLogonRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Checks whether a customer with the given username exists.
     *
     * @param string $username The username to check.
     * @return bool True if the customer exists, false otherwise.
     */
    public function customerExists(string $username): bool
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT COUNT(*) FROM customerlogon WHERE UserName = :username";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("username", $username);
        $stmt->execute();

        $userExists = $stmt->fetchColumn() > 0;

        $this->db->disconnect();

        return $userExists;
    }

    /**
     * Retrieves the active (enabled) customer logon by username.
     *
     * @param string $username The username to search for.
     * @return ?CustomerLogon The CustomerLogon object if found and active; null otherwise.
     */
    public function getActiveCustomerByUsername(string $username): ?CustomerLogon
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM customerlogon WHERE UserName = :username AND State = 1";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("username", $username);
        $stmt->execute();
        $result = $stmt->fetch();

        $this->db->disconnect();

        if ($result !== false) {
            return CustomerLogon::createCustomerLogonFromRecord($result);
        } else {
            return null;
        }

    }

    /**
     * Updates the state (e.g., active/inactive) of a customer's logon.
     *
     * @param int $customerId The customer ID to update.
     * @param int $state The new state value (e.g., 1 for active, 0 for inactive).
     * @return void
     */
    public function updateCustomerState(int $customerId, int $state): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "UPDATE customerlogon SET State = :state WHERE CustomerID = :id";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
        $stmt->bindValue("state", $state, PDO::PARAM_INT);

        $stmt->execute();

        $this->db->disconnect();
    }

    /**
     * Retrieves all customers with their logon data.
     *
     * @return CustomerWithLogonDataArray An array of CustomerWithLogonData objects.
     */
    public function getAllCustomersWithLogonData(): CustomerWithLogonDataArray
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
        ORDER BY LastName, FirstName
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $users = new CustomerWithLogonDataArray();

        foreach ($stmt as $row) {
            $users[] = new CustomerWithLogonData(
                $row['CustomerID'],
                $row['FirstName'],
                $row['LastName'],
                $row['Email'],
                $row['UserName'],
                $row['Address'],
                $row['City'],
                $row['Region'],
                $row['Country'],
                $row['Postal'],
                $row['Phone'],
                $row['Type'],
                $row['State'],
                $row['isAdmin']
            );
        }

        $this->db->disconnect();

        return $users;
    }

    /**
     * Retrieves a customer with logon data by customer ID.
     *
     * @param int $id The ID of the customer to retrieve.
     * @return CustomerWithLogonData The corresponding customer with logon data.
     */
    public function getCustomerDetailsById(int $id): CustomerWithLogonData
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
        WHERE c.CustomerId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        $this->db->disconnect();

        if ($result !== false) {
            return new CustomerWithLogonData(
                $result['CustomerID'],
                $result['FirstName'],
                $result['LastName'],
                $result['Email'],
                $result['UserName'],
                $result['Address'],
                $result['City'],
                $result['Region'],
                $result['Country'],
                $result['Postal'],
                $result['Phone'],
                $result['Type'],
                $result['State'],
                $result['isAdmin']
            );
        } else {
            throw new CustomerNotFoundException($id);
        }
    }

    /**
     * Retrieves a customer with logon data by email address.
     *
     * @param string $email The email address to search for.
     * @return ?CustomerWithLogonData The matching customer or null if not found.
     */
    public function getCustomerDetailsByEmail(string $email): ?CustomerWithLogonData
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
        WHERE c.Email = :email
        ";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("email", $email);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result === false) {
            $this->db->disconnect();
            return null;
        }

        $user = new CustomerWithLogonData(
            $result['CustomerID'],
            $result['FirstName'],
            $result['LastName'],
            $result['Email'],
            $result['UserName'],
            $result['Address'],
            $result['City'],
            $result['Region'],
            $result['Country'],
            $result['Postal'],
            $result['Phone'],
            $result['Type'],
            $result['State'],
            $result['isAdmin']
        );

        $this->db->disconnect();

        return $user;
    }

    /**
     * Updates a customer's basic information.
     *
     * @param int $id Customer ID.
     * @param string $first First name.
     * @param string $last Last name.
     * @param string $email Email address.
     * @param string $address Street address.
     * @param string $city City.
     * @param ?string $region Region/state.
     * @param string $country Country.
     * @param ?string $postal Postal code.
     * @param ?string $phone Phone number.
     * @return void
     */
    public function updateCustomerBasicInfo(
        int $id,
        string $first,
        string $last,
        string $email,
        string $address,
        string $city,
        ?string $region,
        string $country,
        ?string $postal,
        ?string $phone
    ): void {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "UPDATE customers 
        SET FirstName = :first, LastName = :last, Email = :email, Address = :address, City = :city, Region = :region,
        Country = :country, Postal = :postal, Phone = :phone 
        WHERE CustomerId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("first", $first);
        $stmt->bindValue("last", $last);
        $stmt->bindValue("email", $email);
        $stmt->bindValue("address", $address);
        $stmt->bindValue("city", $city);
        $stmt->bindValue("region", $region);
        $stmt->bindValue("country", $country);
        $stmt->bindValue("postal", $postal);
        $stmt->bindValue("phone", $phone);
        $stmt->bindValue("id", $id);

        $stmt->execute();

        $this->db->disconnect();
    }

    /**
     * Updates the admin status of a customer.
     *
     * @param int $customerId The customer ID to update.
     * @param bool $isAdmin Whether the user is an admin (true) or not (false).
     * @return void
     */
    public function updateUserAdmin(int $customerId, bool $isAdmin): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "UPDATE customerlogon SET isAdmin = :isAdmin, DateLastModified = NOW() WHERE CustomerId = :id";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("isAdmin", $isAdmin, PDO::PARAM_BOOL);
        $stmt->bindValue("id", $customerId);

        $stmt->execute();

        $this->db->disconnect();
    }

    /**
     * Counts the number of currently active admin users.
     *
     * @return int The number of active administrators.
     */
    public function countActiveAdmins(): int
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT COUNT(*) FROM customerlogon WHERE isAdmin = 1 AND State = 1";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->execute();

        $count = (int) $stmt->fetchColumn();

        $this->db->disconnect();

        return $count;
    }

    /**
     * Registers a new customer and associated logon data in a single transaction.
     *
     * @param Customer $customer Customer personal information.
     * @param CustomerLogon $logon Login credentials (password should be hashed).
     * @return int The newly created Customer ID.
     * @throws Exception If registration fails.
     */
    public function registerCustomer(Customer $customer, CustomerLogon $logon): int
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Insert login credentials first (let AUTO_INCREMENT handle the CustomerId)
            $hashedPassword = $logon->getPass(); // This should be the complete hash from password_hash()

            $sql = "INSERT INTO customerlogon (UserName, Pass, State, Type, DateJoined, DateLastModified, isAdmin)
            VALUES (:user, :pass, :state, :type, :joined, :modified, :isAdmin)
            ";

            $stmt = $this->db->prepareStatement($sql);

            $stmt->bindValue("user", $logon->getUserName());
            $stmt->bindValue("pass", $hashedPassword);
            $stmt->bindValue("state", $logon->getState());
            $stmt->bindValue("type", $logon->getType());
            $stmt->bindValue("joined", $logon->getDateJoined());
            $stmt->bindValue("modified", $logon->getDateLastModified());
            $stmt->bindValue("isAdmin", false, PDO::PARAM_BOOL);

            $stmt->execute();

            // Get the generated customer ID from customerlogon
            $customerId = (int) $this->db->lastInsertId();

            // Insert customer data with the same CustomerId
            $sql = "INSERT INTO customers (CustomerId, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
            VALUES (:id, :first, :last, :address, :city, :region, :country, :postal, :phone, :email)
            ";

            $stmt = $this->db->prepareStatement($sql);

            $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
            $stmt->bindValue("first", $customer->getFirstName());
            $stmt->bindValue("last", $customer->getLastName());
            $stmt->bindValue("address", $customer->getAddress());
            $stmt->bindValue("city", $customer->getCity());
            $stmt->bindValue("region", $customer->getRegion());
            $stmt->bindValue("country", $customer->getCountry());
            $stmt->bindValue("postal", $customer->getPostal());
            $stmt->bindValue("phone", $customer->getPhone());
            $stmt->bindValue("email", $customer->getEmail());

            $stmt->execute();

            // Commit the transaction
            $this->db->commit();

            $this->db->disconnect();

            return $customerId;

        } catch (Exception $e) {
            // Rollback on any error and throw exception again
            $this->db->rollBack();
            $this->db->disconnect();
            throw $e;
        }
    }

    /**
     * Retrieves a customer by ID.
     *
     * @param int $id Customer ID.
     * @return Customer The Customer object.
     */
    public function getCustomerById(int $id): Customer
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "SELECT * FROM customers WHERE CustomerId = :id";

        $stmt = $this->db->prepareStatement($sql);
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        $customer = Customer::createCustomerFromRecord($result);

        $this->db->disconnect();

        return $customer;
    }

    /**
     * Updates a customer's full information including contact and address.
     *
     * @param int $id Customer ID.
     * @param string $userName user name.
     * @param string $first First name.
     * @param string $last Last name.
     * @param string $address Street address.
     * @param string $city City.
     * @param string|null $region Region/state.
     * @param string $country Country.
     * @param string|null $postal Postal code.
     * @param string|null $phone Phone number.
     * @param string $email Email address.
     * @return void
     */
    public function updateCustomerFullInfo(
        int $id,
        string $userName,
        string $first,
        string $last,
        string $address,
        string $city,
        ?string $region,
        string $country,
        ?string $postal,
        ?string $phone,
        string $email
    ): void {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "UPDATE customers
        SET FirstName = :first, LastName = :last, Address = :address, City = :city, Region = :region, Country = :country,
        Postal = :postal, Phone = :phone, Email = :email
        WHERE CustomerId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("first", $first);
        $stmt->bindValue("last", $last);
        $stmt->bindValue("address", $address);
        $stmt->bindValue("city", $city);
        $stmt->bindValue("region", $region);
        $stmt->bindValue("country", $country);
        $stmt->bindValue("postal", $postal);
        $stmt->bindValue("phone", $phone);
        $stmt->bindValue("email", $email);
        $stmt->bindValue("id", $id);

        $stmt->execute();

        $sqlLogon = "UPDATE customerlogon SET UserName = :username WHERE CustomerId = :id";
        $stmt = $this->db->prepareStatement($sqlLogon);
        $stmt->bindValue("username", $userName);
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $this->db->disconnect();
    }

    /**
     * Updates the customer's password hash.
     *
     * @param int $id Customer ID.
     * @param string $hashed The new hashed password (generated via password_hash()).
     * @return void
     */
    public function updateCustomerPassword(int $id, string $hashed): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $sql = "UPDATE customerlogon
        SET Pass = :pass, DateLastModified = NOW()
        WHERE CustomerId = :id
        ";

        $stmt = $this->db->prepareStatement($sql);

        $stmt->bindValue("pass", $hashed);
        $stmt->bindValue("id", $id);

        $stmt->execute();

        $this->db->disconnect();
    }
}
