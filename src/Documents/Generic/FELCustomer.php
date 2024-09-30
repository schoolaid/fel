<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Enum\CustomerXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELCustomer implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private Customer $customer,
        private FELAddress $Address
    ) {}

    public function asXML(): string
    {
        $attributes = [
            CustomerXML::TaxId->value        => $this->customer->getTaxId(),
            CustomerXML::CustomerName->value => $this->customer->getCustomerName(),
        ];

        if ($this->customer->getEmailCustomer()) {
            $attributes[CustomerXML::EmailCustomer->value] = $this->customer->getEmailCustomer();
        }

        if ($this->customer->getSpecialType()) {
            $attributes[CustomerXML::SpecialType->value] = $this->customer->getSpecialType();
        }

        $xml = $this->buildXML(CustomerXML::Tag->value, $attributes, $this->Address->asXML());

        return $xml;
    }
}
