<?php

namespace SchoolAid\FEL\Contracts;

use Illuminate\Support\Collection;
use SchoolAid\FEL\Documents\InvoiceServiceResponse;

interface InvoiceServiceInterface
{
    public function generate(): InvoiceServiceResponse;
    public function cancel(string $billUuid, string $billDate): Collection;
    // public function download(int $billId): mixed;
}