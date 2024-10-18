<?php
namespace SchoolAid\FEL\Documents\Generator;
use SchoolAid\FEL\Documents\FELDocument;
use SchoolAid\FEL\Documents\Generic\FELAddendas;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\Generic\GeneralData;

class GeneralBill extends FELDocument {
    public function __construct(
        private GeneralData $generalData,
        private FELIssuer $issuer,
        private FELCustomer $customer,
        private FELPhrases $phrases,
        private FELItems $items,
        private FELTotals $totals,
        private ?FELAddendas $addendas,
    ) {}

    public function getSections(): array
    {
        return [
            $this->generalData,
            $this->issuer,
            $this->customer,
            $this->phrases,
            $this->items,
            $this->totals
        ];
    }

    public function getGeneralData(): GeneralData
    {
        return $this->generalData;
    }

    public function getIssuer(): FELIssuer
    {
        return $this->issuer;
    }

    public function getCustomer(): FELCustomer
    {
        return $this->customer;
    }

    public function getPhrases(): FELPhrases
    {
        return $this->phrases;
    }

    public function getItems(): FELItems
    {
        return $this->items;
    }

    public function getTotals(): FELTotals
    {
        return $this->totals;
    }

    public function getAddendas(): ?FELAddendas
    {
        return $this->addendas;
    }

}

// TODO'S 
// 1. Addendas are not necesary allways
// 2. If you add Addendas you need to add Complements to the bill