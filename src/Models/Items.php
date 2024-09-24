<?php

namespace SchoolAid\FEL\Models;


class Items {
    public function __construct(
        private string $line_number,
        private string $product_or_service,
        private int $quantity,
        private string $unit_of_measurement,
        private string $description,
        private float $unit_price,
        private float $price,
        private float $discount,
        private float $total,
    ) {}

    public function getLineNumber(): string
    {
        return $this->line_number;
    }

    public function getProductOrService(): string
    {
        return $this->product_or_service;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitOfMeasurement(): string
    {
        return $this->unit_of_measurement;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUnitPrice(): float
    {
        return $this->unit_price;
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