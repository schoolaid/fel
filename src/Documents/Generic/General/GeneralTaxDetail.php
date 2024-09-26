<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Models\TaxDetail;
use SchoolAid\FEL\Enum\General\TaxNames;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\XMLWritterTrait;
use SchoolAid\FEL\Enum\General\GeneralTaxDetailXML;

class GeneralTaxDetail implements GeneratesXML
{
    use XMLWritterTrait;

    public function __construct(
        private TaxDetail $taxDetail,
        private TaxNames $taxName
    ) {}

    public function asXML(): string
    {
        $subElements = [
            GeneralTaxDetailXML::ShortName->value       => $this->taxName->value,
            GeneralTaxDetailXML::TaxableUnitCode->value => $this->taxDetail->getTaxableUnitCode(),
            GeneralTaxDetailXML::TaxableAmount->value   => $this->taxDetail->getTaxableAmount(),
            GeneralTaxDetailXML::TaxAmount->value       => $this->taxDetail->getTaxAmount(),
            GeneralTaxDetailXML::Total->value           => $this->taxDetail->getTotal(),
        ];

        if ($this->taxDetail->getTaxableUnitCount() !== null) {
            $subElements[GeneralTaxDetailXML::TaxableUnitCount->value] = $this->taxDetail->getTaxableUnitCount();
        }

        $xml = $this->buildXML(GeneralTaxDetailXML::Tag->value, element:$subElements);

        return $xml;
    }
}
