<?php
namespace SchoolAid\FEL\Services;

use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;

class FELCertifyService
{
    private GeneralBill|CancelBill $documentFEL;

    public function __construct(GeneralBill|CancelBill $documentFEL)
    {
        $this->documentFEL = $documentFEL;
    }

    public static function processUnified(GeneralBill|CancelBill $documentFEL): array
    {
        try {
            $xmlBody = $documentFEL->generateXML();

            $action = FELCertifiy::getInstance()
                ->setBody($xmlBody)
                ->submit();

            $response = json_decode($action['body'], true);

            return $response ?? [];

        } catch (\Exception $e) {
            throw new \RuntimeException('Error al procesar el documento FEL: ' . $e->getMessage(), 0, $e);
        }
    }

    public function process(): array
    {
        return self::processUnified($this->documentFEL);
    }
}
