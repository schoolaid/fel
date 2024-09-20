<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\General\IGeneralIssuer;
use SchoolAid\FEL\Enum\General\GeneralIssuerXML;

//Do we need to create a subclass? Previosly this class was abstract but the pest test throws an error

class GeneralIssuer implements IGeneralIssuer
{
    public function getEmailIssuer(): string 
    {
        return '';
    }
    public function getIssuerNit(): string 
    {
        return '';
    }
    public function getCommercialName(): string
    {
        return '';
    }
    public function getIvaAffiliation(): string
    {
        return '';
    }
    public function getIssuerName(): string
    {
        return '';
    }

    public function asXML(): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        xmlwriter_start_element($xw, 'Emisor');

        $attributes = [
            GeneralIssuerXML::EmailIssuer->value => $this->getEmailIssuer(),
            GeneralIssuerXML::IssuerNit->value   => $this->getIssuerNit(),
            GeneralIssuerXML::CommercialName->value => $this->getCommercialName(),
            GeneralIssuerXML::IvaAffiliation->value => $this->getIvaAffiliation(),
            GeneralIssuerXML::IssuerName->value => $this->getIssuerName(),
        ];

        foreach ($attributes as $key => $value) {
            xmlwriter_start_attribute($xw, $key);
            xmlwriter_text($xw, $value);
            xmlwriter_end_attribute($xw);
        }

        xmlwriter_end_element($xw);

        return xmlwriter_output_memory($xw);
    }
}
