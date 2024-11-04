<?php
namespace SchoolAid\FEL\Models;

class InvoiceRows
{
    public function __construct(
        public string $description,
        public int $quantity,
        public float $price,
        public float $total,
        public float $discount = 0,
        public float $priceUnit = 0,
    ) {}

    public function toInfileRows(): array
    {
        return [
            'descripcion' => $this->description,
            'cantidad'    => $this->quantity,
            'precio'      => $this->price,
            'total'       => $this->total,
            'descuento'    => $this->discount,
            'precioUnitario' => $this->priceUnit,
        ];
    }
}
