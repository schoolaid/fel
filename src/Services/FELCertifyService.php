<?php
namespace SchoolAid\FEL\Services;

use PHPUnit\Runner\Baseline\Issue;
use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Models\IssuerCredentials;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;

class FELCertifyService
{
    private GeneralBill|CancelBill $documentFEL;
    private IssuerCredentials $credentials;

    public function __construct(GeneralBill | CancelBill $documentFEL, IssuerCredentials $credentials)
    {
        $this->documentFEL = $documentFEL;
        $this->credentials = $credentials;
    }

    public static function processUnified(GeneralBill | CancelBill $documentFEL, IssuerCredentials $credentials): array {
        try {
            $xmlBody = $documentFEL->generateXML();

            $action = FELCertifiy::getInstance($credentials)
                ->setBody($xmlBody)
                ->submit();

            $response = json_decode($action['body'], true);
            $response['xml'] = $xmlBody;

            return $response ?? [];

        } catch (\Exception $e) {
            throw new \RuntimeException('Error al procesar el documento FEL: ' . $e->getMessage(), 0, $e);
        }
    }

    public function process(): array
    {
        return self::processUnified($this->documentFEL, $this->credentials );
    }
}
