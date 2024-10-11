<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Enum\TaxNames;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\TaxDetailXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELTaxDetail implements GeneratesXML
{
    use HasXML;

    public function __construct(
        private float $amount,
        private TaxNames $tax 
    ) {}

    public function calculateTaxAmounts(): array
    {
        $calculatedTax = round($this->amount / (1 + $this->tax->percentage()), 2);
        $taxAmount     = $this->amount - $calculatedTax;
        $taxableAmount = $calculatedTax;

        return [
            'shortName' => $this->tax->value,
            'taxableAmount' => $taxableAmount,
            'taxAmount'     => $taxAmount,
        ];
    }

    public function asXML(): string
    {
        $taxAmounts = $this->calculateTaxAmounts();
        $taxableAmount = $taxAmounts['taxableAmount'];
        $taxAmount = $taxAmounts['taxAmount'];

        $subElements = [
            TaxDetailXML::ShortName->value       => $this->tax->value,
            TaxDetailXML::TaxableUnitCode->value => $this->tax->taxableCode(),
            TaxDetailXML::TaxableAmount->value   => $taxableAmount,
            TaxDetailXML::TaxAmount->value       => $taxAmount,
        ];

        $xml = $this->buildXML(TaxDetailXML::Tag->value, element: $subElements);

        return $xml;
    }
}
