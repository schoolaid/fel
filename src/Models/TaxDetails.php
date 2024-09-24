<?php
namespace SchoolAid\FEL\Models;

// private $nombre_corto;

// private $codigo_unidad_gravable;

// private $monto_gravable;

// private $cantidad_unidades_gravables;

// private $monto_impuesto;

// private $total;

class TaxDetails 
{
    public function __construct(
        private string $short_name,
        private int $taxable_unit_code,
        private float $taxable_amount,
        private ?float $taxable_unit_count,
        private float $tax_amount,
        private float $total
    )
    {}

    public function getShortName(): string
    {
        return $this->short_name;
    }

    public function getTaxableUnitCode(): int
    {
        return $this->taxable_unit_code;
    }

    public function getTaxableAmount(): float
    {
        return $this->taxable_amount;
    }

    public function getTaxableUnitCount(): ?float
    {
        return $this->taxable_unit_count;
    }

    public function getTaxAmount(): float
    {
        return $this->tax_amount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

}