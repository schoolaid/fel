<?php

namespace Schoolaid\Fel\Models;

use Schoolaid\Fel\Enums\TaxEnum;

class FelItem
{
    public int $lineNumber;
    public string $goodOrService;
    public float $quantity;
    public string $unitMeasure;
    public string $description;
    public float $unitPrice;
    public float $price;
    public float $discount;
    public array $taxes = [];
    public float $total;
    
    public function __construct(
        int $lineNumber = 1,
        string $goodOrService = 'S',
        float $unitPrice = 0,
        string $unitMeasure = 'UND',
        string $description = '',
        float $price = 0,
        float $quantity = 1,
        float $discount = 0,
        array $taxes = [],
        float $total = 0
    ) {
        $this->lineNumber = $lineNumber;
        $this->goodOrService = $goodOrService;
        $this->quantity = $quantity;
        $this->unitMeasure = $unitMeasure;
        $this->description = $description;
        $this->unitPrice = $unitPrice;
        $this->price = $price;
        $this->discount = $discount;
        
        // Añadir impuestos si se proporcionan
        foreach ($taxes as $tax) {
            if ($tax instanceof FelTax) {
                $this->taxes[] = $tax;
            } else {
                $this->taxes[] = new FelTax(
                    isset($tax['name']) ? $tax['name'] : TaxEnum::IVA,
                    isset($tax['amount']) ? $tax['amount'] : $this->price
                );
            }
        }
        
        // Establecer el total si se proporciona, o calcularlo
        $this->total = $total > 0 ? $total : $this->calculateTotal();
    }
    
    /**
     * Agrega un impuesto al ítem
     *
     * @param FelTax $tax
     * @return self
     */
    public function addTax(FelTax $tax): self
    {
        $this->taxes[] = $tax;
        $this->total = $this->calculateTotal();
        return $this;
    }
    
    /**
     * Calcula el total del ítem incluyendo el precio y los impuestos
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        // Por simplicidad, estamos considerando que el total es igual al precio
        // Se podría implementar una lógica más compleja si es necesario
        return $this->total;
    }
    
    public function toArray(): array
    {
        return [
            'lineNumber' => $this->lineNumber,
            'goodOrService' => $this->goodOrService,
            'quantity' => $this->quantity,
            'unitMeasure' => $this->unitMeasure,
            'description' => $this->description,
            'unitPrice' => $this->unitPrice,
            'price' => $this->price,
            'discount' => $this->discount,
            'taxes' => array_map(fn($tax) => $tax->toArray(), $this->taxes),
            'total' => $this->total
        ];
    }
}
