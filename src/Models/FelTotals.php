<?php

namespace Schoolaid\Fel\Models;

class FelTotals
{
    public ?FelTaxTotal $taxTotal;
    public float $grandTotal;
    
    public function __construct(
        FelTaxTotal $taxTotal = null,
        float       $grandTotal = 0
    ) {
        $this->taxTotal = $taxTotal;
        $this->grandTotal = $grandTotal;
    }
    
    public function toArray(): array
    {
        return [
            'taxTotals' => $this->taxTotal?->toArray(),
            'grandTotal' => $this->grandTotal
        ];
    }
} 