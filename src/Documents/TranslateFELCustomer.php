<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;

class TranslateFELCustomer
{
    public function __construct(
        public string $nitCustomer,
        public string $emailCustomer,
        public string $nameCustomer,
        public string $address,
        public ?string $postalCode,
        public string $city,
        public string $state,
        public ?string $country,
    )
    {
        $this->postalCode = $postalCode ?? '01001';
        $this->country = $country ?? 'GT';
    }

    public function settingFELCustomer(): FELCustomer
    {
        return new FELCustomer(
            new Customer(
                $this->nitCustomer,
                $this->emailCustomer,
                $this->nameCustomer,
                null
            ),
            new FELAddress(
                new Address(
                    $this->address,
                    $this->postalCode,
                    $this->city,
                    $this->state,
                    $this->country
                ),
                AddressType::Customer
            )
        );
    }
}