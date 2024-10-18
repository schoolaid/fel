<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Enum\TaxNames;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\TotalsXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELTotals implements GeneratesXML
{
    use HasXML;
    private FELTaxTotal $taxTotals;

    public function __construct(private array $items)
    {
        $this->taxTotals = $this->calculateTaxes();
    }

    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    private function calculateTaxes(): FELTaxTotal
    {
        $taxTotals = [];
        foreach ($this->items as $item) {
            $tax         = new FELTaxDetail($item->getTotal(), TaxNames::IVA); // Adjust this line to use the correct tax, probably will change to validate or calcluate by an array
            $taxTotals[] = $tax->calculateTaxAmounts();
        }

        return new FELTaxTotal($taxTotals);
    }

    public function asXML(): string
    {
        $total    = $this->calculateTotal();
        $taxesXML = $this->taxTotals->asXML();

        $subElements = [
            TotalsXML::TaxTotalPlural->value => $taxesXML,
            TotalsXML::GrantTotal->value     => $total,
        ];

        $xml = $this->buildXML(TotalsXML::Tag->value, element: $subElements);

        return $xml;
    }
}
