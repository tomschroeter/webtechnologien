<?php

/**
 * Custom exception class for when no customer with the given ID was found.
 */
class CustomerNotFoundException extends Exception
{
    private int $customerId;

    public function __construct(int $customerId)
    {
        $this->customerId = $customerId;
 
        $message = "Customer with ID {$customerId} couldn't be found.";
        parent::__construct($message);
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }
}
