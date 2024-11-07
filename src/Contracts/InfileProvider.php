<?php
namespace SchoolAid\FEL\Contracts;

use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELTotals;

interface InfileProvider
{
    public function issuerInfo(): FELIssuer;
    public function customerInfo(): FELCustomer;
    public function itemsInfo(): FELItems;
    public function totals(): FELTotals;

}