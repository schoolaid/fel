<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\TotalsXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELTotals implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private float $grandTotal,
        private FELTaxTotal $taxTotals
    ) {
        // $this->taxTotals = $this->calculateTaxes();
    }

    public function asXML(): string
    {
        $taxesXML = $this->taxTotals->asXML();

        $subElements = [
            TotalsXML::TaxTotalPlural->value => $taxesXML,
            TotalsXML::GrantTotal->value     => $this->grandTotal,
        ];

        $xml = $this->buildXML(TotalsXML::Tag->value, element: $subElements);

        return $xml;
    }
}
