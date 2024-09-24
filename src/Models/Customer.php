<?php

namespace SchoolAid\FEL\Models;

class Customer {
    public function __construct(
        private string $idCustomer,
        private string $emailCustomer,
        private string $customerName,
        private string $specialType
    ) {}
    
    public function getIdCustomer(): string
    {
        return $this->idCustomer;
    }

    public function getEmailCustomer(): string
    {
        return $this->emailCustomer;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getSpecialType(): string
    {
        return $this->specialType;
    }
}