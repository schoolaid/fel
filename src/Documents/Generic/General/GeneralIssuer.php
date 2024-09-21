<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\GeneralIssuerXML;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Enum\General\IVAAffiliationType;
use SchoolAid\FEL\Traits\XMLWritterTrait;


class GeneralIssuer implements GeneratesXML {
    use XMLWritterTrait;
    public function __construct(
        private Issuer $issuer,
        private GeneralAddress $genralAddress,
        private IVAAffiliationType $ivaAffiliation
    ) {}

    public function asXML(): string
    {
        $attributes = [
            GeneralIssuerXML::EmailIssuer->value => $this->issuer->getEmailIssuer(),
            GeneralIssuerXML::IssuerNit->value   => $this->issuer->getIssuerNit(),
            GeneralIssuerXML::CommercialName->value => $this->issuer->getCommercialName(),
            GeneralIssuerXML::IVAAffiliation->value => $this->ivaAffiliation->value,
            GeneralIssuerXML::IssuerName->value => $this->issuer->getIssuerName(),
            GeneralIssuerXML::Address->value => $this->genralAddress->asXML()
        ];

        return $this->buildXML('dte:Emisor',$attributes, 'attributes');
    }
}