<?php

namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Enum\General\GeneralAddressXML;
use SchoolAid\FEL\Models\Address;

class GeneralAddress implements GeneratesXML
{
    public function __construct(
        private Address $address,
        private AddressType $addressType
    ) {}

    public function asXML(): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        xmlwriter_start_element($xw, $this->addressType->value);
            $elements_p = [
                GeneralAddressXML::address->value => $this->address->getAddress(),
                GeneralAddressXML::postal_code->value => $this->address->getPostalCode(),
                GeneralAddressXML::city->value => $this->address->getCity(),
                GeneralAddressXML::state->value => $this->address->getState(),
                GeneralAddressXML::country->value => $this->address->getCountry()
            ];

            foreach ($elements_p as $key => $value) {
                xmlwriter_start_element($xw, $key);
                xmlwriter_text($xw, $value);
                xmlwriter_end_element($xw);
            }
            xmlwriter_end_element($xw);
        
        return xmlwriter_output_memory($xw);
    }
}
