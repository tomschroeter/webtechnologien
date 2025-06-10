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
        $stmt = $this->db->prepareStatement("SELECT COUNT(*) FROM customerlogon WHERE UserName = :username");
        $stmt->bindValue("username", $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getNextCustomerId(): int
    {
        $stmt = $this->db->prepareStatement("SELECT MAX(CustomerId) + 1 AS nextId FROM customers");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 1;
    }

    public function getActiveUserByUsername(string $username): ?array
    {
        $stmt = $this->db->prepareStatement("
        SELECT * FROM customerlogon WHERE UserName = :username AND State = 1
    ");
        $stmt->bindValue("username", $username);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateUserState(int $customerId, int $state): void
    {
        $stmt = $this->db->prepareStatement("UPDATE customerlogon SET State = :state WHERE CustomerID = :id");
        $stmt->bindValue("id", $customerId, PDO::PARAM_INT);
        $stmt->bindValue("state", $state, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllUsersWithLogonData(): array
    {
        $stmt = $this->db->prepareStatement("
        SELECT c.CustomerID, FirstName, LastName, Email, UserName, Type, State
        FROM customers c
        JOIN customerlogon cl ON c.CustomerID = cl.CustomerID
        ORDER BY LastName, FirstName
    ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserDetailsById(int $id): ?array
    {
        $stmt = $this->db->prepareStatement("
        SELECT c.FirstName, c.LastName, c.Email, cl.UserName, cl.Type
        FROM customers c
        JOIN customerlogon cl ON c.CustomerId = cl.CustomerId
        WHERE c.CustomerId = :id
    ");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateCustomerBasicInfo(int $id, string $first, string $last, string $email): void
    {
        $stmt = $this->db->prepareStatement("
        UPDATE customers SET FirstName = :first, LastName = :last, Email = :email WHERE CustomerId = :id
    ");
        $stmt->bindValue("first", $first);
        $stmt->bindValue("last", $last);
        $stmt->bindValue("email", $email);
        $stmt->bindValue("id", $id);
        $stmt->execute();
    }

    public function updateUserType(int $customerId, int $type): void
    {
        $stmt = $this->db->prepareStatement("
        UPDATE customerlogon SET Type = :type, DateLastModified = NOW() WHERE CustomerId = :id
    ");
        $stmt->bindValue("type", $type, PDO::PARAM_INT);
        $stmt->bindValue("id", $customerId);
        $stmt->execute();
    }



    public function insertCustomer(Customer $customer, int $id): void
    {
        $stmt = $this->db->prepareStatement("
            INSERT INTO customers (CustomerId, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
            VALUES (:id, :first, :last, :address, :city, :region, :country, :postal, :phone, :email)
        ");
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
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
    }

    public function insertLogon(CustomerLogon $logon): void
    {
        $stmt = $this->db->prepareStatement("
            INSERT INTO customerlogon (CustomerId, UserName, Pass, State, Type, DateJoined, DateLastModified)
            VALUES (:id, :user, :pass, :state, :type, :joined, :modified)
        ");
        $stmt->bindValue("id", $logon->getCustomerId(), PDO::PARAM_INT);
        $stmt->bindValue("user", $logon->getUserName());
        $stmt->bindValue("pass", $logon->getPass());
        $stmt->bindValue("state", $logon->getState());
        $stmt->bindValue("type", $logon->getType());
        $stmt->bindValue("joined", $logon->getDateJoined());
        $stmt->bindValue("modified", $logon->getDateLastModified());
        $stmt->execute();
    }
}
