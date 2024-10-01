<?php

use SchoolAid\FEL\Documents\Generic\FELAddendas;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Models\Addenda;
use SchoolAid\FEL\Models\Phrase;

// it(' Issuer XML', function () {
//     $Issuer = new FELIssuer(
//         new Issuer(
//             'issuer@test.com',
//             '123456789',
//             'Comercial Name',
//             'Issuer Name'
//         ),
//         new FELAddress(new Address(
//             '14 avenida A',
//             '01006',
//             'Guatemala',
//             'Guatemala',
//             'Guatemala'
//         ), AddressType::Issuer),
//         IVAAffiliationType::General
//     );

//     echo ' Issuer XML';
//     echo "\n";
//     echo $Issuer->asXML();
// });

// it('Items XML', function () {

//     $item = [
//         new Item(
//             '1',
//             'B',
//             1,
//             'Unidad',
//             'Producto de Prueba',
//             100.00,
//             100.00,
//             0.00,
//             100.00
//         ),
//         new Item(
//             '2',
//             'B',
//             1,
//             'Unidad',
//             'Producto de Prueba 2',
//             100.00,
//             100.00,
//             0.00,
//             100.00
//         ),
//     ];

//     $Items = new FELItems($item, ProductServiceType::Product);

//     echo 'Items XML';
//     echo "\n";
//     echo $Items->asXML();
// });


// Testing items with totals calculation

// it('Calculating totals and Items XML', function () {
    
//         $item = [
//             new Item(
//                 '1',
//                 'B',
//                 1,
//                 'Unidad',
//                 'Producto de Prueba',
//                 100.00,
//                 100.00,
//                 0.00,
//                 100.00
//             ),
//             new Item(
//                 '2',
//                 'B',
//                 1,
//                 'Unidad',
//                 'Producto de Prueba 2',
//                 100.00,
//                 100.00,
//                 0.00,
//                 100.00
//             ),
//         ];
    
//         $Items = new FELItems($item, ProductServiceType::Product);

//         $Totals = new FELTotals($item);

//         echo 'Items XML';
//         echo "\n";
//         echo $Items->asXML();
//         echo "\n";
//         echo 'Total: ' . $Totals->calculateTotal();
//         echo "\n";
// });

// it('Phrases XML', function () {
    
//         $phrases = [
//             new Phrase(
//                 '1',
//                 '1',
//                 '1',
//                 '2021-01-01'
//             ),
//             new Phrase(
//                 '2',
//                 '2',
//                 '2',
//                 '2021-01-01'
//             ),
//         ];
    
//         $Phrases = new FELPhrases($phrases);
    
//         echo 'Phrases XML';
//         echo "\n";
//         echo $Phrases->asXML();
// });

it('Addendas XML', function() {
    $addendas = [
        new Addenda(
            '1',
            '1',
            '1',
            '2021-01-01'
        ),
        new Addenda(
            '2',
            '2',
            '2',
            '2021-01-01'
        ),
    ];

    $Addendas = new FELAddendas($addendas);

    echo 'Addendas XML';
    echo "\n";
    echo $Addendas->asXML();
});