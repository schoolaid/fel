<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Enum\AddressXML;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELAddress implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private Address $address,
        private AddressType $addressType
    ) {}

    public function asXML(): string
    {

        $subElements = [
            AddressXML::Address->value    => $this->address->getAddress(),
            AddressXML::PostalCode->value => $this->address->getPostalCode(),
            AddressXML::City->value       => $this->address->getCity(),
            AddressXML::State->value      => $this->address->getState(),
            AddressXML::Country->value    => $this->address->getCountry(),
        ];

        return $this->buildXML($this->addressType->value, element: $subElements);
    }
}
