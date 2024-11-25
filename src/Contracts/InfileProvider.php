<?php
namespace SchoolAid\FEL\Contracts;

use SchoolAid\FEL\Models\IssuerCredentials;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELCustomer;

interface InfileProvider
{
    public function isValidEntity(): bool;
    public function issuerInfo(): FELIssuer;
    public function customerInfo(): FELCustomer;
    public function itemsInfo(): FELItems;

    public function issuerCredentials(): IssuerCredentials;

}
