<?php
namespace SchoolAid\FEL\Actions;

use SchoolAid\FEL\Actions\Interfaces\FELCertAction;

class FELCertifiy extends FELCertAction
{
    public function url(): string
    {
        return 'procesounificado/transaccion/v2/xml';
    }

}