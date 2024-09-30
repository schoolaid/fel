<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Models\TaxDetail;
use SchoolAid\FEL\Enum\TaxNames;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\TaxDetailXML;

class TaxDetail implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private TaxDetail $taxDetail,
        private TaxNames $taxName
    ) {}

    public function asXML(): string
    {
        $subElements = [
            TaxDetailXML::ShortName->value       => $this->taxName->value,
            TaxDetailXML::TaxableUnitCode->value => $this->taxDetail->getTaxableUnitCode(),
            TaxDetailXML::TaxableAmount->value   => $this->taxDetail->getTaxableAmount(),
            TaxDetailXML::TaxAmount->value       => $this->taxDetail->getTaxAmount(),
            TaxDetailXML::Total->value           => $this->taxDetail->getTotal(),
        ];

        if ($this->taxDetail->getTaxableUnitCount()) {
            $subElements[TaxDetailXML::TaxableUnitCount->value] = $this->taxDetail->getTaxableUnitCount();
        }

        $xml = $this->buildXML(TaxDetailXML::Tag->value, element: $subElements);

        return $xml;
    }
}
