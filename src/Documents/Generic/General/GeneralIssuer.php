<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\XMLWritterTrait;
use SchoolAid\FEL\Enum\General\GeneralIssuerXML;
use SchoolAid\FEL\Enum\General\IVAAffiliationType;

class GeneralIssuer implements GeneratesXML
{
    use XMLWritterTrait;
    public function __construct(
        private Issuer $issuer,
        private GeneralAddress $genralAddress,
        private IVAAffiliationType $ivaAffiliation
    ) {}

    public function asXML(): string
    {
        $attributes = [
            GeneralIssuerXML::IssuerNit->value      => $this->issuer->getIssuerNit(),
            GeneralIssuerXML::CommercialName->value => $this->issuer->getCommercialName(),
            GeneralIssuerXML::IVAAffiliation->value => $this->ivaAffiliation->value,
            GeneralIssuerXML::IssuerName->value     => $this->issuer->getIssuerName(),
        ];

        if ($this->issuer->getEmailIssuer() !== null) {
            $attributes[GeneralIssuerXML::EmailIssuer->value] = $this->issuer->getEmailIssuer();
        }

        $xml = $this->buildXML(GeneralIssuerXML::Tag->value, $attributes, $this->genralAddress->asXML());

        return $xml;
    }
}
