<?php
namespace Tests\Feature;

use PHPUnit\Runner\Baseline\Issue;
use Tests\BaseTestCase;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Phrase;
use SchoolAid\FEL\Models\Addenda;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Actions\FELCertifiy;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\CancelBill;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Documents\Generic\FELCancel;

class BillTest extends BaseTestCase
{
    /**
     * Tes for bill
     *
     */

    public function testExample()
    {
        $generalData   = new BillGeneralData();
        $generalIssuer = new FELIssuer(
            new Issuer(
                'issuer@test.com',
                1,
                '11201169K',
                'Comercial Name',
                'Issuer Name'
            ),
            new FELAddress(new Address(
                '14 avenida A',
                '01006',
                'Guatemala',
                'Guatemala',
                'GT'
            ), AddressType::Issuer),
            IVAAffiliationType::General
        );

        $generalCustomer = new FELCustomer(
            new Customer(
                'CF',
                'customer@test.com',
                'Customer Name',
                null
            ),
            new FELAddress(new Address(
                '14 avenida A',
                '01006',
                'Guatemala',
                'Guatemala',
                'GT'
            ), AddressType::Customer)
        );

        $items = [
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

        $felItems = new FELItems($items, ProductServiceType::Product);
        $totals   = new FELTotals($items);

        $addendas = [
            new Addenda('1', 'name1'),
            new Addenda('2', 'name2'),
        ];

        $phrases = [
            new Phrase('1', '1', null, null),
        ];

        $documentFEL = new GeneralBill(
            $generalData,
            $generalIssuer,
            $generalCustomer,
            new FELPhrases($phrases),
            $felItems,
            $totals,
            null
        );

        try {
            $result = $documentFEL->generateXML();
            $action = FELCertifiy::getInstance()
                ->setBody($result)
                ->submit();
            // echo "XML enviado exitosamente: " . $action['body'];
            $response = json_decode($action['body'], true);

            $currentDate = new \DateTime('now', new \DateTimeZone('-6:00'));
            $formattedDate = $currentDate->format('Y-m-d\TH:i:sP');

            // Datos para cancelar el documento
            $cancelDocument = new CancelBill(
                new FELCancel(
                    $formattedDate,   // Fecha actual en formato correcto
                    '11201169K', // ID del usuario del emisor
                    $generalData->getIssueDateTime(),           // Fecha del documento original
                    'CF',
                    $response['uuid'],          
                    'test'                      
                )
            );

            $resultCancel = $cancelDocument->generateAnnulationXML();
            // echo "XML enviado exitosamente: " . $resultCancel;
            $action = FELCertifiy::getInstance()
                ->setBody($resultCancel)
                ->submit();
            echo "Factura cancelada" . $action['body'];
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
