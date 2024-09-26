<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\XMLWritterTrait;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Enum\General\GeneralAddressXML;

class GeneralAddress implements GeneratesXML
{
    use XMLWritterTrait;
    public function __construct(
        private Address $address,
        private AddressType $addressType
    ) {}

    public function asXML(): string
    {

        $sub_elements = [
            GeneralAddressXML::Address->value    => $this->address->getAddress(),
            GeneralAddressXML::PostalCode->value => $this->address->getPostalCode(),
            GeneralAddressXML::City->value       => $this->address->getCity(),
            GeneralAddressXML::State->value      => $this->address->getState(),
            GeneralAddressXML::Country->value    => $this->address->getCountry(),
        ];

        return $this->buildXML($this->addressType->value, element: $sub_elements);
    }
}
