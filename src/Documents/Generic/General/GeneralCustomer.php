<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\GeneralCustomerXML;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Traits\XMLWritterTrait;

class GeneralCustomer implements GeneratesXML
{   
    use XMLWritterTrait;

    
    public function __construct(
        private Customer $customer,
        private GeneralAddress $generalAddress
    ) {}

    public function asXML(): string
    {
        $attributes = [
            GeneralCustomerXML::IdCustomer->value => $this->customer->getIdCustomer(),
            GeneralCustomerXML::CustomerName->value => $this->customer->getCustomerName(),
        ];

        if($this->customer->getEmailCustomer() !== null) {
            $attributes[GeneralCustomerXML::EmailCustomer->value] = $this->customer->getEmailCustomer();
        }

        if($this->customer->getSpecialType() !== null) {
            $attributes[GeneralCustomerXML::SpecialType->value] = $this->customer->getSpecialType();
        }

        $xml = $this->buildXML(GeneralCustomerXML::Tag->value, $attributes, $this->generalAddress->asXML());

        return $xml;
    }
}