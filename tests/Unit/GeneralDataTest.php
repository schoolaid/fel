<?php

use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Phrase;
use SchoolAid\FEL\Models\Addenda;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELAddendas;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Documents\Generic\FELTaxTotal;

it('GeneralBill XML generator', function () {
    $generalData   = new BillGeneralData();
    $generalIssuer = new FELIssuer(
        new Issuer(
            'issuer@test.com',
            1,
            '123456789',
            'Comercial Name',
            'Issuer Name'
        ),
        new FELAddress(new Address(
            '14 avenida A',
            '01006',
            'Guatemala',
            'Guatemala',
            'Guatemala'
        ), AddressType::Issuer),
        IVAAffiliationType::General
    );

    $generalCustomer = new FELCustomer(
        new Customer(
            '123456789',
            'customer@test.com',
            'Customer Name',
            'Special Type'
        ),
        new FELAddress(new Address(
            '14 avenida A',
            '01006',
            'Guatemala',
            'Guatemala',
            'Guatemala'
        ), AddressType::Customer)
    );

    $item = [
        new Item(
            '1',
            'B',
            1,
            'Unidad',
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
            'Unidad',
            'Producto de Prueba 2',
            100.00,
            100.00,
            0.00,
            100.00
        ),
    ];

    $Items = new FELItems($item, ProductServiceType::Product);

    $Totals = new FELTotals($item);
    //Deberia ser lo mismo para TAX

    $addendas = [
        new Addenda(
            '1',
            'name1',
        ),
        new Addenda(
            '2',
            'name2',
        ),
    ];

    $phrases = [
        new Phrase(
            '1',
            '1',
            null,
            null,
        ),
    ];

    $documentFEL = new GeneralBill(
        $generalData,
        $generalIssuer,
        $generalCustomer,
        new FELPhrases($phrases),
        $Items,
        $Totals,
        null
    );

    echo 'GeneralBill XML generator';
    echo "\n";
    echo $documentFEL->generateXML();

    //Crear Assertions para validar contenido del XMl
});


// it('XML tax totals tes', function () {
//     //     $item = [
//     //     new Item(
//     //         '1',
//     //         'B',
//     //         1,
//     //         'Unidad',
//     //         'Producto de Prueba',
//     //         100.00,
//     //         100.00,
//     //         0.00,
//     //         100.00
//     //     ),
//     //     new Item(
//     //         '2',
//     //         'B',
//     //         1,
//     //         'Unidad',
//     //         'Producto de Prueba 2',
//     //         100.00,
//     //         100.00,
//     //         0.00,
//     //         100.00
//     //     ),
//     // ];

//     // $Totals = new FELTotals($item);
//     // echo 'XML totals tes';
//     // echo "\n";
//     // echo $Totals->asXML();

//     $taxes = [
//         [
//             'shortName' => 'IVA',
//             'taxableAmount' => 100.00,
//             'taxAmount' => 12.00,
//         ],
//         [
//             'shortName' => 'IVA',
//             'taxableAmount' => 100.00,
//             'taxAmount' => 12.00,
//         ],
//         [
//             'shortName' => 'IVA',
//             'taxableAmount' => 100.00,
//             'taxAmount' => 12.00,
//         ],
//         [
//             'shortName' => 'IVA',
//             'taxableAmount' => 100.00,
//             'taxAmount' => 12.00,
//         ],
//     ];

//     $Totals = new FELTaxTotal($taxes);
//     echo 'XML totals tes';
//     echo "\n";
//     echo $Totals->asXML();
// });