<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\General\GeneralCustomerXML;

class GeneralCustomer implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private Customer $customer,
        private GeneralAddress $generalAddress
    ) {}

    public function asXML(): string
    {
        $attributes = [
            GeneralCustomerXML::TaxId->value        => $this->customer->getTaxId(),
            GeneralCustomerXML::CustomerName->value => $this->customer->getCustomerName(),
        ];

        if ($this->customer->getEmailCustomer()) {
            $attributes[GeneralCustomerXML::EmailCustomer->value] = $this->customer->getEmailCustomer();
        }

        if ($this->customer->getSpecialType()) {
            $attributes[GeneralCustomerXML::SpecialType->value] = $this->customer->getSpecialType();
        }

        $xml = $this->buildXML(GeneralCustomerXML::Tag->value, $attributes, $this->generalAddress->asXML());

        return $xml;
    }
}
