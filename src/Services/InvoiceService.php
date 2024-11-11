<?php
namespace SchoolAid\FEL\Services;

use SchoolAid\FEL\Models\Phrase;
use Illuminate\Support\Collection;
use SchoolAid\FEL\Contracts\InfileProvider;
use SchoolAid\FEL\Services\FELCertifyService;
use SchoolAid\FEL\Documents\Generic\FELCancel;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Contracts\InvoiceServiceInterface;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(public InfileProvider $provider)
    {
    }

    public function validarNit()
    {
        $customerData = $this->provider->customerInfo();
        $nit          = $customerData->getCustomer()->getTaxId();

        return preg_match("/(([1-9])+([0-9])*([0-9]|K))|(([1-9]+[0-9]){12,13})|(CF)|([A-Z0-9]{3,18})/", $nit);
    }

    public function generate(): Collection
    {
        $phrases = [
            new Phrase('1', '1', null, null),
        ];

        $generalData    = new BillGeneralData();
        $issuerData     = $this->provider->issuerInfo();
        $customerData   = $this->provider->customerInfo();
        $generalPhrases = new FELPhrases($phrases);
        $itemsData      = $this->provider->itemsInfo();
        $totalsData     = $this->provider->totalsInfo();

        $document = new GeneralBill(
            $generalData,
            $issuerData,
            $customerData,
            $generalPhrases,
            $itemsData,
            $totalsData,
            null//Addendas goes here
        );

        $certify = FELCertifyService::processUnified($document);

        return collect($certify);
        // return new InvoiceServiceResponse(true, 'bill_issued', $certify);
    }

    public function cancel(string $billUuid, string $billDate): Collection
    {
        $currentDate   = new \DateTime('now', new \DateTimeZone('-6:00'));
        $formattedDate = $currentDate->format('Y-m-d\TH:i:sP');
        $issuerData    = $this->provider->issuerInfo();
        $customerData  = $this->provider->customerInfo();

        $issuerNit   = $issuerData->getIssuer()->getIssuerNit();
        $customerNit = $customerData->getCustomer()->getTaxId();
        $cancelData  = new FELCancel(
            $formattedDate, // Fecha actual en formato correcto
            $issuerNit, // ID del usuario del emisor
            $billDate, // Fecha del documento original
            $customerNit,
            $billUuid,
            'CANCELACION'
        );

        $document = new CancelBill(
            $cancelData
        );

        $certify = FELCertifyService::processUnified($document);

        return collect($certify);
    }
}
