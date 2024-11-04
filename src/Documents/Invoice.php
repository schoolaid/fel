<?php 

namespace SchoolAid\FEL\Documents;

use App\Enums\BillStatusEnum;
use App\Models\Bill;
use App\Utils\Infile\Fel\Items;
use App\Utils\Infile\Fel\Total;
use App\Utils\Infile\Fel\Frases;
use App\Utils\Infile\Fel\FelVoid;
use App\Utils\Infile\GenerateXml;
use App\Utils\Infile\Fel\TaxTotal;
use App\Utils\Infile\Fel\FelService;
use App\Utils\Infile\Fel\TaxDetails;
use App\Utils\Infile\Fel\DocumentFel;
use App\Utils\Infile\Fel\GeneralData;
use App\Utils\Infile\Fel\IssuingData;
use App\Utils\Infile\Fel\ReceiverData;
use App\Utils\Infile\Fel\FelServiceConnection;
use Illuminate\Support\Facades\Log;
use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELCancel;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\Issuer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Models\Issuer as IssuerModel;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Phrase;

// Necesitamos obtener el XML generado y aqui mandar a llamar el certificador


class Invoice
{
    public function __construct(
        public string $nombre,
        public string $nit,
        public string $direccion,
        public string $codigoPostal,
        public ?string $correo
    ) {
    }

    public function validarNit()
    {
        return preg_match("/(([1-9])+([0-9])*([0-9]|K))|(([1-9]+[0-9]){12,13})|(CF)|([A-Z0-9]{3,18})/", $this->nit);
    }

    public function emitirFactura(string $idFactura,
        float $granTotal,
        array $orden,
        Issuer $issuer) {

        if (!$this->validarNit()) {
            return collect(["success" => false, "message" => "Invalid NIT"]);
        }
        
        // DATOS EMISOR
        $issuerData = new IssuerModel(
            $issuer->email,
            $issuer->officeCode,
            $issuer->nit,
            $issuer->commercialName,
            $issuer->name
        );
        $adressIssuer = new FELAddress(
            new Address(
                $issuer->address,
                "01001",
                $issuer->city,
                $issuer->state,
                "GT"
            ),
            AddressType::Issuer
        );
        $issuerAfil = IVAAffiliationType::General;

        $issuerFELSection = new FELIssuer($issuerData, $adressIssuer, $issuerAfil);

        // DATOS GENERALES
        $generalData = new BillGeneralData;

        //DATOS RECEPTOR
        $customerData = new Customer(
            $this->nit,
            $this->nombre,
            $this->correo,
            null
        );

        $customerAddress = new FELAddress(
            new Address(
                $this->direccion,
                $this->codigoPostal,
                "Guatemala",
                "Guatemala",
                "GT"
            ),
            AddressType::Customer
        );

        $customerFELSection= new FELCustomer($customerData, $customerAddress);
        
        //ITEMS 
        $arr_items = [];
        for ($i = 0; $i < count($orden); $i++) {
            $item  = $orden[$i];
            $items = new Item(
                $i + 1,
                "S",
                $item["cantidad"],
                "UND",
                $item["descripcion"],
                $item["precioUnitario"],
                $item["precio"],
                $item["descuento"] ?? 0.0,
                $item["total"]
            );

            $arr_items[] = $items;
        }

        $itemsFELSection = new FELItems($arr_items, ProductServiceType::Service);

        //TOTAL
        $total = new FELTotals($orden);

        //FRASES
        $frase = [new Phrase(1,1,null,null)];
        $phrasesFELSection = new FELPhrases($frase);

        //XML
        $generarSeccionesXML = new GeneralBill($generalData, $issuerFELSection, $customerFELSection, $phrasesFELSection, $itemsFELSection, $total, null);
        $resultXML = $generarSeccionesXML->generateXML();

        // certificacion
        $action = FELCertifiy::getInstance()
                ->setBody($resultXML)
                ->submit();
        
        $response = json_decode($action['body'], true);

        if ($response["resultado"]) {

            return collect([
                "success"     => true,
                "message"     => $response["descripcion"],
                "uuid"        => $response["uuid"],
                "xml"         => $resultXML,
                "serie"       => $response["serie"],
                "number"      => $response["numero"],
                "certificate" => $response["xml_certificado"],
            ]);
        } else {
            return collect(["success" => false, "message" => "Certification failed", 'errors' => $response["descripcion_errores"]]);
        }
    }

    public function anularFactura(string $uuid, string $documentDate, Issuer $issuer)
    {
        $currentDate   = new \DateTime('now', new \DateTimeZone('-6:00'));
        $formattedDate = $currentDate->format('Y-m-d\TH:i:sP');

        $cancelStructure = new FELCancel(
            $formattedDate,
            $this->nit,
            $documentDate,
            $issuer->nit,
            $uuid,
            "CANCELACIÓN",
        );

        $joinCancel = new CancelBill($cancelStructure);
        $resultXML  = $joinCancel->generateXML();

        $action = FELCertifiy::getInstance()
                ->setBody($resultXML)
                ->submit();

        $response = json_decode($action['body'], true);

        if ($response["resultado"]) {

            return collect([
                "success" => true,
                "description" => 'Factura anulada',
                "message" => "Success",
            ]);
        } else {
            Log::error("Error al anular: {$uuid}" . $response["descripcion"]);
            return collect([
                "success" => false,
                "message" => "Certification failed",
                "description" => $response["descripcion"],
            ]);
        }
    }
}
