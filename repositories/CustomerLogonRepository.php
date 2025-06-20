<?php

require_once dirname(__DIR__) . "/classes/Artist.php";
require_once dirname(__DIR__) . "/dtos/CustomerWithLogonData.php";

class CustomerLogonRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function userExists(string $username): bool
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("SELECT COUNT(*) FROM customerlogon WHERE UserName = :username");
        $stmt->bindValue("username", $username);
        $stmt->execute();

        $userExists = $stmt->fetchColumn() > 0;

        $this->db->disconnect();

        return $userExists;
    }

    public function getActiveUserByUsername(string $username): CustomerLogon | null
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("SELECT * FROM customerlogon WHERE UserName = :username AND State = 1");

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

    public function updateUserState(int $customerId, int $state): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("UPDATE customerlogon SET State = :state WHERE CustomerID = :id");
        $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
        $stmt->bindValue("state", $state, PDO::PARAM_INT);
        $stmt->execute();

        $this->db->disconnect();
    }

    /**
     * @return CustomerWithLogonData[]
     */
    public function getAllUsersWithLogonData()
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
        ORDER BY LastName, FirstName
    ");
        $stmt->execute();

        $users = [];

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

    public function getUserDetailsById(int $id): CustomerWithLogonData
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
        WHERE c.CustomerId = :id
    ");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

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

    public function getUserDetailsByEmail(string $email): CustomerWithLogonData
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
            SELECT c.CustomerID, c.FirstName, c.LastName, c.Email, c.Address, c.City, c.Region, c.Country, c.Postal, c.Phone, cl.UserName, cl.Type, cl.State, cl.isAdmin
            FROM customers c
            JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
            WHERE c.Email = :email
        ");
        $stmt->bindValue("email", $email);
        $stmt->execute();
        $result = $stmt->fetch();
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

        $stmt = $this->db->prepareStatement("
            UPDATE customers 
            SET FirstName = :first, 
                LastName = :last, 
                Email = :email, 
                Address = :address, 
                City = :city, 
                Region = :region,
                Country = :country, 
                Postal = :postal, 
                Phone = :phone 
            WHERE CustomerId = :id
        ");
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

    public function updateUserAdmin(int $customerId, bool $isAdmin): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        UPDATE customerlogon SET isAdmin = :isAdmin, DateLastModified = NOW() WHERE CustomerId = :id
    ");
        $stmt->bindValue("isAdmin", $isAdmin, PDO::PARAM_BOOL);
        $stmt->bindValue("id", $customerId);
        $stmt->execute();

        $this->db->disconnect();
    }

    public function countActiveAdmins(): int
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("SELECT COUNT(*) FROM customerlogon WHERE isAdmin = 1 AND State = 1");
        $stmt->execute();

        $count = (int) $stmt->fetchColumn();

        $this->db->disconnect();

        return $count;
    }

    /**
     * Atomically registers a new customer with login credentials.
     * This method prevents race conditions by handling everything in a single transaction.
     * Uses AUTO_INCREMENT for CustomerId in customerlogon table.
     * 
     * Password Security:
     * - Uses password_hash() to generate a secure hash that includes salt, algorithm info, and cost
     * - The complete hash is stored in the Pass field (e.g., "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi")
     * - The salt is extracted from the hash and stored separately in the Salt field for database requirements
     * - password_verify() still handles verification automatically using the complete hash
     * 
     * @param Customer $customer The customer data
     * @param CustomerLogon $logon The login credentials (Pass should be hashed with password_hash())
     * @return int The generated customer ID
     * @throws Exception If registration fails
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

            $stmt = $this->db->prepareStatement("
                INSERT INTO customerlogon (UserName, Pass, State, Type, DateJoined, DateLastModified, isAdmin)
                VALUES (:user, :pass, :state, :type, :joined, :modified, :isAdmin)
            ");
            $stmt->bindValue("user", $logon->getUserName());
            $stmt->bindValue("pass", $hashedPassword);
            $stmt->bindValue("state", $logon->getState());
            $stmt->bindValue("type", $logon->getType());
            $stmt->bindValue("joined", $logon->getDateJoined());
            $stmt->bindValue("modified", $logon->getDateLastModified());
            $stmt->bindValue("isAdmin", false, PDO::PARAM_BOOL); // New users are not admins by default
            $stmt->execute();

            // Get the generated customer ID from customerlogon
            $customerId = (int) $this->db->lastInsertId();

            // Insert customer data with the same CustomerId
            $stmt = $this->db->prepareStatement("
                INSERT INTO customers (CustomerId, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
                VALUES (:id, :first, :last, :address, :city, :region, :country, :postal, :phone, :email)
            ");
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
            // Rollback on any error
            $this->db->rollBack();
            $this->db->disconnect();
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function getCustomerById(int $id): Customer
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }
        $stmt = $this->db->prepareStatement("SELECT * FROM customers WHERE CustomerId = :id");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        $customer = Customer::createCustomerFromRecord($result);

        $this->db->disconnect();

        return $customer;
    }

    public function updateCustomerFullInfo(
        int $id,
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
        $stmt = $this->db->prepareStatement(
            "UPDATE customers SET FirstName = :first, LastName = :last, Address = :address, City = :city, Region = :region, Country = :country, Postal = :postal, Phone = :phone, Email = :email WHERE CustomerId = :id"
        );
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
        $this->db->disconnect();
    }

    public function updateCustomerPassword(int $id, string $hashed): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }
        $stmt = $this->db->prepareStatement("UPDATE customerlogon SET Pass = :pass, DateLastModified = NOW() WHERE CustomerId = :id");
        $stmt->bindValue("pass", $hashed);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $this->db->disconnect();
    }
}
