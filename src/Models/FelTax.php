<?php

namespace Schoolaid\Fel\Models;

use Schoolaid\Fel\Enums\TaxEnum;

class FelTax
{
    public int $taxableUnitCode;
    public float $taxableAmount;
    public float $taxAmount;

    public function __construct(
        public readonly TaxEnum $shortName = TaxEnum::IVA,
        public readonly float   $amount = 0,
    ) {
        $this->taxableUnitCode =  $this->shortName->taxableCode();
        $this->calculate();
    }

    public function toArray(): array
    {
        return [
            'shortName' => $this->shortName->value,
            'taxableUnitCode' => $this->taxableUnitCode,
            'taxableAmount' => $this->taxableAmount,
            'taxAmount' => $this->taxAmount
        ];
    }

    public function calculate(): void
    {
        $this->taxableAmount = round($this->amount / (1 + $this->shortName->percentage()), 2);
        $this->taxAmount     = $this->amount - $this->taxableAmount;
        $this->taxableUnitCode = $this->shortName->taxableCode();
    }
}