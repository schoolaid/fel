<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Contracts\InfileProvider;
use SchoolAid\FEL\Contracts\InvoiceServiceInterface;
use SchoolAid\FEL\Responses\InvoiceServiceResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Documents\Generic\FELCancel;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Models\Phrase;
use SchoolAid\FEL\Services\FELCertifyService;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(public InfileProvider $provider)
    {
    }

    public function generate(): InvoiceServiceResponse
    {
        if (!$this->provider->isValidEntity()) {
            return new InvoiceServiceResponse(false, 'invalid_entity', null);
        }

        $phrases = [
            new Phrase('1', '1', null, null),
        ];

        $generalData = new BillGeneralData();
        $issuerData = $this->provider->issuerInfo();
        $customerData = $this->provider->customerInfo();
        $generalPhrases = new FELPhrases($phrases);
        $itemsData = $this->provider->itemsInfo();
        $totalsData = $this->provider->totalsInfo();
        
        $document = new GeneralBill(
            $generalData,
            $issuerData,
            $customerData,
            $generalPhrases,
            $itemsData,
            $totalsData,
            null //Addendas goes here
        );

        $certify = FELCertifyService::processUnified($document);

        if (!$certify['success']) {
            Log::error($this->provider->tenantId());
            Log::error("Error al facturar: {$this->provider->number()}".json_encode($certify));

            return new InvoiceServiceResponse(false, 'error_issuing', $certify);
        }

        return new InvoiceServiceResponse(true, 'bill_issued', $certify);
    }

    public function cancel(string $billUuid, string $billDate): Collection
    {
        $currentDate   = new \DateTime('now', new \DateTimeZone('-6:00'));
        $formattedDate = $currentDate->format('Y-m-d\TH:i:sP');
        $issuerData    = $this->provider->issuerInfo();
        $customerData = $this->provider->customerInfo();
        
        $issuerNit = $issuerData->getIssuer()->getIssuerNit();
        $customerNit = $customerData->getCustomer()->getTaxId();
        $cancelData = new FELCancel(
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

    // public function download(): string
    // {
    //     // Logic to download the invoice without store in DB
    //     // Pending logic
    //     return "";
    // }
}