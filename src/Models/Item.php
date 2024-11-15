<?php
namespace SchoolAid\FEL\Models;

use SchoolAid\FEL\Enum\ProductServiceType;

class Item
{
    public function __construct(
        private string $lineNumber,
        private string $productOrService,
        private int $quantity,
        private string $unitOfMeasurement,
        private string $description,
        private float $unitPrice,
        private float $price,
        private float $discount,
        private float $total,
    ) {}

    public function getLineNumber(): string
    {
        return $this->lineNumber;
    }

    public function getProductOrService(): string
    {
        return $this->productOrService;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitOfMeasurement(): string
    {
        return $this->unitOfMeasurement;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

}
