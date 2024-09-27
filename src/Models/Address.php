<?php
namespace SchoolAid\FEL\Models;

class Address
{

    public function __construct(
        private string $address,
        private string $postalCode,
        private string $city,
        private string $state,
        private string $country
    ) {}

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
