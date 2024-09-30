<?php
namespace SchoolAid\FEL\Documents\Bill;

use SchoolAid\FEL\Documents\Generic\GeneralData;

class BillGeneralData extends GeneralData
{

    public function getExportation(): ?string
    {
        return null;
    }

    public function getType(): string
    {
        return 'GEN';
    }
}
