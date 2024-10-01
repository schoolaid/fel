<?php

namespace SchoolAid\Documents\Generators;


// use SchoolAid\Documents\Generic\BillHeader;
use SchoolAid\Documents\Generic\FELTotals;
use SchoolAid\Documents\Generic\FELItems;
use SchoolAid\Documents\Generic\FELCustomer;
use SchoolAid\Documents\Generic\FELIssuer;


// class BillGeneralGenerator
// {
//     public function __construct(
//         private FELCustomer $customer,
//         private FELIssuer $issuer,
//         private FELItems $items,
//         private FELTotals $totals
//     ) {}

//     public function generate(): string
//     {
//         $header = $this->generateHeader();
//         $customer = $this->customer->asXML();
//         $issuer = $this->issuer->asXML();
//         $items = $this->items->asXML();
//         $totals = $this->totals->asXML();

//         $xml = 