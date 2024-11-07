<?php
namespace SchoolAid\FEL\Contracts;

use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\TranslateFELCustomer;
use SchoolAid\FEL\Documents\TranslateFELIssuer;

interface InfileProvider
{
    public function isValidEntity(): bool;
    public function issuerInfo(): TranslateFELIssuer;
    public function customerInfo(): TranslateFELCustomer;
    public function itemsInfo(): FELItems;

}