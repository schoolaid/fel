<?php

namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Enum\General\GeneralAddressXML;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Traits\XMLWritterTrait;

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
            GeneralAddressXML::address->value => $this->address->getAddress(),
            GeneralAddressXML::postal_code->value => $this->address->getPostalCode(),
            GeneralAddressXML::city->value => $this->address->getCity(),
            GeneralAddressXML::state->value => $this->address->getState(),
            GeneralAddressXML::country->value => $this->address->getCountry()
        ];


        return $this->buildXML($this->addressType->value,[], $sub_elements);
    }
}
