<?php

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

    public function getActiveUserByUsername(string $username): ?array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("SELECT * FROM customerlogon WHERE UserName = :username AND State = 1");

        $stmt->bindValue("username", $username);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $returnValue = $result ?: null;

        $this->db->disconnect();

        return $returnValue;
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

    public function getAllUsersWithLogonData(): array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        SELECT c.CustomerID, FirstName, LastName, Email, UserName, Type, State, isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
        ORDER BY LastName, FirstName
    ");
        $stmt->execute();
        $result = $stmt->fetchAll();

        $this->db->disconnect();

        return $result;
    }

    public function getUserDetailsById(int $id): ?array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        SELECT c.FirstName, c.LastName, c.Email, cl.UserName, cl.Type, cl.isAdmin
        FROM customers c
        JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
        WHERE c.CustomerId = :id
    ");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        $returnValue = $user ?: null;

        $this->db->disconnect();

        return $returnValue;
    }

    public function updateCustomerBasicInfo(int $id, string $first, string $last, string $email): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }

        $stmt = $this->db->prepareStatement("
        UPDATE customers SET FirstName = :first, LastName = :last, Email = :email WHERE CustomerId = :id
    ");
        $stmt->bindValue("first", $first);
        $stmt->bindValue("last", $last);
        $stmt->bindValue("email", $email);
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
            $salt = $this->extractSaltFromHash($hashedPassword);
            
            $stmt = $this->db->prepareStatement("
                INSERT INTO customerlogon (UserName, Pass, Salt, State, Type, DateJoined, DateLastModified, isAdmin)
                VALUES (:user, :pass, :salt, :state, :type, :joined, :modified, :isAdmin)
            ");
            $stmt->bindValue("user", $logon->getUserName());
            $stmt->bindValue("pass", $hashedPassword);
            $stmt->bindValue("salt", $salt);
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

    /**
     * Extracts the salt from a password hash generated by password_hash().
     * 
     * Password hash format: $algorithm$cost$saltAndHash
     * For bcrypt ($2y$): $2y$10$22-character-salt + 31-character-hash
     * 
     * @param string $hash The complete password hash
     * @return string The extracted salt
     */
    private function extractSaltFromHash(string $hash): string
    {
        // Check if it's a bcrypt hash ($2y$ or $2a$ or $2x$)
        if (preg_match('/^\$2[axy]\$\d+\$(.{22})/', $hash, $matches)) {
            return $matches[1]; // Return the 22-character salt
        }

        throw new Exception("Unknown hash type in db");
    }

    public function getCustomerById(int $id): ?array
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }
        $stmt = $this->db->prepareStatement("SELECT * FROM customers WHERE CustomerId = :id");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $customer = $stmt->fetch();
        $this->db->disconnect();
        return $customer ?: null;
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

    public function updateCustomerPassword(int $id, string $hashed, string $salt): void
    {
        if (!$this->db->isConnected()) {
            $this->db->connect();
        }
        $stmt = $this->db->prepareStatement("UPDATE customerlogon SET Pass = :pass, Salt = :salt, DateLastModified = NOW() WHERE CustomerId = :id");
        $stmt->bindValue("pass", $hashed);
        $stmt->bindValue("salt", $salt);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $this->db->disconnect();
    }
}
