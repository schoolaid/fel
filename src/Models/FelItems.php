<?php

namespace Schoolaid\Fel\Models;

use Schoolaid\Fel\Enums\DocumentTypeEnum;

class FelItems
{
    /**
     * @var FelItem[]
     */
    public array $items = [];
    
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if ($item instanceof FelItem) {
                $this->items[] = $item;
            } else {
                $this->items[] = new FelItem(
                    $item['lineNumber'] ?? 1,
                    $item['goodOrService'] ?? 'S',
                    $item['unitPrice'] ?? 0,
                    $item['unitMeasure'] ?? 'UND',
                    $item['description'] ?? '',
                    $item['price'] ?? 0,
                    $item['quantity'] ?? 1,
                    $item['discount'] ?? 0,
                    $item['taxes'] ?? [],
                    $item['total'] ?? 0
                );
            }
        }
    }
    
    /**
     * Agrega un ítem a la colección
     *
     * @param FelItem $item
     * @return self
     */
    public function addItem(FelItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }
    
    /**
     * Calcula los totales sumando todos los ítems
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total;
        }
        
        return $total;
    }
    
    public function toArray(): array
    {
        return array_map(fn(FelItem $item) => $item->toArray(), $this->items);
    }
} 