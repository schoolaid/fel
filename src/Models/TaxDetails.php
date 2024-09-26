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
        private string $shortName,
        private int $taxableUnitCode,
        private float $taxableAmount,
        private ?float $taxableUnitCount,
        private float $taxAmount,
        private float $total
    ) {}

    public function getShortName(): string
    {
        return $this->shortName;
    }

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
