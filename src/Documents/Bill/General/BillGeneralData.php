<?php
namespace SchoolAid\FEL\Documents\Bill\General;

use SchoolAid\FEL\Documents\Generic\General\GeneralData;

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