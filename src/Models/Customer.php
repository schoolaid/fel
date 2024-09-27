<?php
namespace SchoolAid\FEL\Models;

class Customer
{
    public function __construct(
        private string $taxId,
        private string $emailCustomer,
        private string $customerName,
        private string $specialType
    ) {}

    public function getTaxId(): string
    {
        return $this->taxId;
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
