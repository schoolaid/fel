<?php
namespace SchoolAid\FEL\Models;

class TaxDetail
{
    public function __construct(
        private int $taxableUnitCode
    ) {}

    public function getTaxableUnitCode(): int
    {
        return $this->taxableUnitCode;
    }

}
