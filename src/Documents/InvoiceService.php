<?php
namespace SchoolAid\FEL\Documents;

use Illuminate\Database\Eloquent\Collection;
use SchoolAid\FEL\Contracts\InfileProvider;
use SchoolAid\FEL\Documents\InvoiceServiceResponse;
use SchoolAid\FEL\Contracts\InvoiceServiceInterface;
use Illuminate\Support\Facades\Log;

//Here goes the structure of the different methods, generate, cancel, download, etc...

class InvoiceService implements InvoiceServiceInterface
{
    public InfileProvider $provider;
    public function __construct($provider = null)
    {
        $this->provider = $provider;
    }

    public function generate(): InvoiceServiceResponse
    {
        if (!$this->provider->isValidEntity()) {
            return new InvoiceServiceResponse(false, 'invalid_entity', null);
        }

        $infileInvoice = $this->provider->invoiceHeaders()->toInfileInvoice();

        $response = $infileInvoice->emitirFactura(
            $this->provider->number(),
            $this->provider->total(),
            $this->provider->invoiceRows(),
            $this->provider->issuer()
        );

        if (!$response['success']) {
            Log::error($this->provider->tenantId());
            Log::error("Error al facturar: {$this->provider->number()}" . json_encode($response));

            return new InvoiceServiceResponse(false, 'error_issuing', $response->toArray());
        }

        return new InvoiceServiceResponse(true, 'bill_issued', $response->toArray());
    }

    public function cancel(string $billUuid, string $billDate): Collection
    {

        $infileInvoice = $this->provider->invoiceHeaders()->toInfileInvoice();
        $response = $infileInvoice->anularFactura($billUuid, $billDate, $this->provider->issuer());

        return $response;
    }
}
