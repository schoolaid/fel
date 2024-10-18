<?php
namespace SchoolAid\FEL\Documents\Generator;

use SchoolAid\FEL\Documents\FELDocument;
use SchoolAid\FEL\Documents\Generic\FELCancel;

class CancelBill extends FELDocument
{
    public function __construct(
        private FELCancel $cancel
    ) {}

    public function getSections(): array
    {
        return [
            $this->cancel
        ];
    }
}
