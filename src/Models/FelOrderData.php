<?php

namespace Schoolaid\Fel\Models;

class FelOrderData
{
    public string $orderId;
    public string $orderDate;
    public string $reference;
    public array $additionalData = [];
    
    public function __construct(
        string $orderId = '',
        string $orderDate = '',
        string $reference = '',
        array $additionalData = []
    ) {
        $this->orderId = $orderId;
        $this->orderDate = $orderDate ?: date('Y-m-d');
        $this->reference = $reference;
        $this->additionalData = $additionalData;
    }
    
    public function toArray(): array
    {
        return [
            'orderId' => $this->orderId,
            'orderDate' => $this->orderDate,
            'reference' => $this->reference,
            'additionalData' => $this->additionalData
        ];
    }
} 