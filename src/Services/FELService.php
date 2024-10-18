<?php
namespace SchoolAid\FEL\Services;

use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Documents\Generator\GeneralBill;

class FELService
{
    public function __construct(
        private GeneralBill $documentFEL
    )
    {}

    public function processUnified () {
        try {
            $result = $this->documentFEL->generateXML();
            $action = FELCertifiy::getInstance()
                ->setBody($result)
                ->submit();
            return $action['body'];
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
