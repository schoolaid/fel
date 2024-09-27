<?php
namespace SchoolAid\FEL\Models;

class TaxDetail
{
    public function __construct(
        private int $taxableUnitCode,
        private float $taxableAmount,
        private ?float $taxableUnitCount,
        private float $taxAmount,
        private float $total
    ) {}

    public function getTaxableUnitCode(): int
    {
        return $this->taxableUnitCode;
    }

    public function getTaxableAmount(): float
    {
        return $this->taxableAmount;
    }

    public function getTaxableUnitCount(): ?float
    {
        return $this->taxableUnitCount;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

}
