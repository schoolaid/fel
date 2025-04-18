<?php

namespace Schoolaid\Fel\Models;

class FelTaxTotal
{
    public array $totals;
    public function __construct(
        public readonly array $taxes,
    ) {
        $this->totals = [];
        $this->calculate();
    }

    public function calculate(): void
    {
        $totals = [];
        foreach ($this->taxes as $tax) {
            $shortName = $tax->shortName->value;
            if (!isset($totals[$shortName])) {
                $totals[$shortName] = 0;
            }
            $totals[$shortName] += $tax->taxAmount;
        }

        $this->totals = $totals;
    }

    public function toArray(): array
    {
        return $this->totals;
    }
} 