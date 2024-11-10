<?php
namespace SchoolAid\FEL\Contracts;

use Illuminate\Support\Collection;

interface InvoiceServiceInterface
{
    public function generate(): Collection;
    public function cancel(string $billUuid, string $billDate): Collection;
}
