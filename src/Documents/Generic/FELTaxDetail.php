<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Models\TaxDetail;
use SchoolAid\FEL\Enum\TaxDetailXML;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\TaxNames;

class FELTaxDetail implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private Item $item,
        private TaxDetail $taxDetail,
        private TaxNames $taxName
    ) {}

    public function asXML(): string
    {
        $calculatedTax  = round($this->item["total"] / 1.12, 2);
        $taxAmount     = $this->item["total"] - $calculatedTax;
        $taxableAmount = $calculatedTax;

        $subElements = [
            TaxDetailXML::ShortName->value       => $this->taxName->value,
            TaxDetailXML::TaxableUnitCode->value => $this->taxDetail->getTaxableUnitCode(),
            TaxDetailXML::TaxableAmount->value   => $taxableAmount,
            TaxDetailXML::TaxAmount->value       => $taxAmount,
        ];

        $xml = $this->buildXML(TaxDetailXML::Tag->value, element: $subElements);

        return $xml;
    }
}
