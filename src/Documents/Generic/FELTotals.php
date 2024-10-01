<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\TotalsXML;
use SchoolAid\FEL\Traits\HasXML;

class FELTotals implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $items
    ) {}

    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    public function asXML(): string
    {
        $total = $this->calculateTotal();

        $subElements = [
            TotalsXML::GrantTotal->value => $total,
        ];

        $xml = $this->buildXML(TotalsXML::Tag->value, element: $subElements);
        return $xml;
        
    }
}
