<?php
namespace SchoolAid\FEL\Models;

class Total
{
    public function __construct(
        private float $grantTotal
    )
    {}

    public function getGrantTotal(): float
    {
        return $this->grantTotal;
    }
}
