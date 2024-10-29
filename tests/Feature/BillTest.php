<?php
namespace Tests\Feature;

use SimpleXMLElement;
use Tests\BaseTestCase;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Phrase;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELCancel;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;

// Definimos las constantes comunes
const DEFAULT_ADDRESS = [
    'street'     => '14 avenida A',
    'zipcode'    => '01006',
    'city'       => 'Guatemala',
    'department' => 'Guatemala',
    'country'    => 'GT',
];

// Funcion para crear una dirección
function createAddress(array $addressData, $type): FELAddress
{
    return new FELAddress(
        new Address(
            $addressData['street'],
            $addressData['zipcode'],
            $addressData['city'],
            $addressData['department'],
            $addressData['country']
        ),
        $type
    );
}

// Funcion para crear un emisor
function createIssuer(): FELIssuer
{
    return new FELIssuer(
        new Issuer(
            'issuer@test.com',
            1,
            '11201169K', //NIT
            'Comercial Name',
            'Issuer Name'
        ),
        createAddress(DEFAULT_ADDRESS, AddressType::Issuer),
        IVAAffiliationType::General
    );
}

// Funcion para crear un cliente
function createCustomer(): FELCustomer
{
    return new FELCustomer(
        new Customer(
            'CF',
            'customer@test.com',
            'Customer Name',
            null
        ),
        createAddress(DEFAULT_ADDRESS, AddressType::Customer)
    );
}

// Funcion para crear items
function createItems(): array
{
    return [
        new Item(
            '1',
            'B',
            1,
            'UND',
            'Producto de Prueba',
            100.00,
            100.00,
            0.00,
            100.00
        ),
        new Item(
            '2',
            'B',
            1,
            'UND',
            'Producto de Prueba 2',
            100.00,
            100.00,
            0.00,
            100.00
        ),
    ];
}

// Funcion principal para generar el documento
function generateDocument(): array
{
    $generalData     = new BillGeneralData();
    $generalIssuer   = createIssuer();
    $generalCustomer = createCustomer();
    $items           = createItems();

    $felItems = new FELItems($items, ProductServiceType::Product);
    $totals   = new FELTotals($items);

    $phrases = [
        new Phrase('1', '1', null, null),
    ];
    $generalPhrases = new FELPhrases($phrases);

    return [
        $generalData,
        $generalIssuer,
        $generalCustomer,
        $generalPhrases,
        $felItems,
        $totals,
        null,
    ];
}

// Funcion para crear generalBill
function createGeneralBill(): GeneralBill
{
    $document = new GeneralBill(...generateDocument());

    return $document;
}

//Funcion para crear FelCancel
function createFelCancel($uuid, $generateDocument): CancelBill
{
    $currentDate   = new \DateTime('now', new \DateTimeZone('-6:00'));
    $formattedDate = $currentDate->format('Y-m-d\TH:i:sP');

    $generalData = $generateDocument->getGeneralData();

    $issuerNit   = $generateDocument->getIssuer()->issuer->getIssuerNit();
    $customerNit = $generateDocument->getCustomer()->customer->getTaxId();

    return new CancelBill(
        new FELCancel(
            $formattedDate, // Fecha actual en formato correcto
            $issuerNit, // ID del usuario del emisor
            $generalData->getIssueDateTime(), // Fecha del documento original
            $customerNit,
            $uuid,
            'test'
        )
    );
}

// Funcion auxiliar para validar la existencia de elementos en el XML
function validateXmlElement(SimpleXMLElement $xml, string $xpath): void
{
    expect($xml->xpath($xpath))->not->toBeEmpty();
}

class BillTest extends BaseTestCase
{
    /**
     * Test for bill generation and cancellation
     */
    public function testBillGenerationAndCancellation()
    {

        $documentFEL = createGeneralBill();

        try {
            $result = $documentFEL->generateXML();
            $xml    = new SimpleXMLElement($result);

            $elementsToValidate = [
                '//dte:Totales',
                '//dte:TotalImpuestos',
                '//dte:TotalImpuesto',
                '//dte:GranTotal',
                '//dte:Items',
                '//dte:Item',
                '//dte:Impuestos',
                '//dte:Impuesto',
                '//dte:Emisor',
                '//dte:Receptor',
                '//dte:DatosGenerales',
                '//dte:DireccionEmisor',
                '//dte:DireccionReceptor',
            ];

            // Validar la existencia de cada elemento
            foreach ($elementsToValidate as $xpath) {
                validateXmlElement($xml, $xpath);
            }

            // Validar el valor de GranTotal
            $granTotalNodes = $xml->xpath('//dte:GranTotal');
            if (!empty($granTotalNodes)) {
                $granTotal = (float) $granTotalNodes[0];
                expect($granTotal)->toBeGreaterThanOrEqual(0);
            } else {
                throw new \Exception('El elemento GranTotal no se encontró en el XML.');
            }

            // Certificando Documento
            $action = FELCertifiy::getInstance()
                ->setBody($result)
                ->submit();

            // Decode response
            $response = json_decode($action['body'], true);
            echo "UUID generado exitosamente: " . $response['uuid'];

            expect($response)->toHaveKey('uuid');

            $cancelDocument = createFelCancel($response['uuid'], $documentFEL);
            $resultCancel   = $cancelDocument->generateAnnulationXML();
            echo "XML enviado exitosamente: " . $resultCancel;

            // echo "XML enviado exitosamente: " . $resultCancel;
            $action = FELCertifiy::getInstance()
                ->setBody($resultCancel)
                ->submit();

                echo "Documento anulado exitosamente: " . $action['body'];
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

}
