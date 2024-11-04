<?php
namespace SchoolAid\FEL\Contracts;

use SchoolAid\FEL\Documents\Issuer;
use SchoolAid\FEL\Models\InvoiceHeaders;

interface InfileProvider
{
    public function invoiceRows(): array;
    public function invoiceHeaders(): InvoiceHeaders;
    public function isValidEntity(): bool;

    public function total(): float;
    public function number(): int;
    public function orderId(): int;
    public function tenantId(): string;
    public function issuer(): Issuer;
}
