<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\IssuerXML;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\IVAAffiliationType;

class FELIssuer implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private Issuer $issuer,
        private FELAddress $genralAddress,
        private IVAAffiliationType $ivaAffiliationType
    ) {}

    public function asXML(): string
    {
        $attributes = [
            IssuerXML::IssuerNit->value      => $this->issuer->getIssuerNit(),
            IssuerXML::CommercialName->value => $this->issuer->getCommercialName(),
            IssuerXML::IVAAffiliation->value => $this->ivaAffiliationType->value,
            IssuerXML::IssuerName->value     => $this->issuer->getIssuerName(),
            IssuerXML::EstablishmentCode->value => $this->issuer->getEstablishmentCode(),
        ];

        if ($this->issuer->getEmailIssuer()) {
            $attributes[IssuerXML::EmailIssuer->value] = $this->issuer->getEmailIssuer();
        }

        $xml = $this->buildXML(IssuerXML::Tag->value, $attributes, $this->genralAddress->asXML());

        return $xml;
    }
}
