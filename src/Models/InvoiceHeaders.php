<?php
namespace SchoolAid\FEL\Models;

use SchoolAid\FEL\Documents\Invoice;

class InvoiceHeaders
{
    public function __construct(public string $fullName,
        public string $nit,
        public string $address,
        public ?string $email,
        public string $postalCode = '') {

    }

    public function toInfileInvoice(): Invoice
    {
        return new Invoice($this->fullName, $this->nit, $this->address, $this->postalCode, $this->email);
    }
}
