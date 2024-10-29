<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\CancelBillXML;
use SchoolAid\FEL\Traits\HasXML;

class FELCancel implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private string $dateCancel,
        private string $idIssuer,
        private string $dateDocumentToCancel,
        private string $idCustomer,
        private string $documentNumber,
        private string $reason
    ) {}

    public function asXML(): string
    {
        $attributes = [
            CancelBillXML::IdAttribute->value => CancelBillXML::Id->value,
            CancelBillXML::DateCancel->value => $this->dateCancel,
            CancelBillXML::IdIssuer->value => $this->idIssuer,
            CancelBillXML::DateDocumentToCancel->value => $this->dateDocumentToCancel,
            CancelBillXML::IdCustomer->value => $this->idCustomer,
            CancelBillXML::DocumentNumber->value => $this->documentNumber,
            CancelBillXML::Reason->value => $this->reason,
        ];

        return $this->buildXML(CancelBillXML::Tag->value, $attributes);
    }
}