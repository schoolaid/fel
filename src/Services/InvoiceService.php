<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Contracts\InfileProvider;
use SchoolAid\FEL\Contracts\InvoiceServiceInterface;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(public InfileProvider $infileProvider)
    {
    }

    public function generate(): void
    {
        // Logic to generate the invoice without store in DB
    }

    public function cancel(): string
    {
        // Logic to cancel the invoice without store in DB
    }

    public function download(): string
    {
        // Logic to download the invoice without store in DB
    }
}