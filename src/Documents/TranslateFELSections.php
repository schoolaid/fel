<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Contracts\InfileProvider;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELTotals;

class TranslateFELSections implements InfileProvider
{
    public function __construct(
        public string $customerName,
        public string $customerNit,
        public ?string $customerAddress = '',
        public ?string $customerEmail = '',
        public string $customerPostalCode = '',
        public string $issuerEmail,
        public string $establishmentCode,
        public string $issuerNit,
        public string $commercialName,
        public string $issuerName,
        public string $issuerAddress,
        public string $issuerPostalCode,
        public string $issuerCity,
        public string $issuerState,
        public string $issuerCountry,
        public string $issuerType,
        public array $items,
        public ?array $phrases = []
    )
    {}

    public function issuerInfo(): FELIssuer
    {
        return new FELIssuer($this->fullName, $this->nit, $this->address, $this->email, $this->postalCode);
    }

    public function customerInfo(): FELCustomer
    {
        return new FELCustomer($this->fullName, $this->nit, $this->address, $this->email, $this->postalCode);
    }

    public function itemsInfo(): FELItems
    {
        return new FELItems($this->items);
    }

    public function totals(): FELTotals
    {
        return new FELTotals($this->items);
    }

    public function phrases(): FELPhrases
    {
        return new FELPhrases();
    }
}
