<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\TotalsXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELTaxTotal implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $taxes,
    ) {}

    public function calculateTotal(): array
    {
        $totals = [];
        foreach ($this->taxes as $tax) {
            $shortName = $tax['shortName'];
            if (!isset($totals[$shortName])) {
                $totals[$shortName] = 0;
            }
            $totals[$shortName] += $tax['taxAmount'];
        }

        return $totals;
    }

    public function asXML(): string
    {
        $totals_agrupated = $this->calculateTotal();
        $taxes            = [];

        foreach ($totals_agrupated as $shortName => $taxAmount) {
            $attributes = [
                TotalsXML::TaxShortName->value => $shortName,
                TotalsXML::TaxAmount->value    => $taxAmount,
            ];

            $taxes[] = $this->buildXML(TotalsXML::TaxTotalSingular->value, $attributes);
        }

        return implode("", $taxes);

    }
}
