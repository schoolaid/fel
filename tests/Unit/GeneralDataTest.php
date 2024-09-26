<?php

use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Models\TaxDetail;
use SchoolAid\FEL\Enum\General\TaxNames;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Enum\General\IVAAffiliationType;
use SchoolAid\FEL\Enum\General\ProductServiceType;
use SchoolAid\FEL\Documents\Bill\General\BillGeneralData;
use SchoolAid\FEL\Documents\Generic\General\GeneralItems;
use SchoolAid\FEL\Documents\Generic\General\GeneralIssuer;
use SchoolAid\FEL\Documents\Generic\General\GeneralAddress;
use SchoolAid\FEL\Documents\Generic\General\GeneralCustomer;
use SchoolAid\FEL\Documents\Generic\General\GeneralTaxDetail;

// it('Bill General Data generates the XML correctly', function () {
//     $generalData = new BillGeneralData();

//     echo $generalData->asXML();
// });

// it('General Issuer XML', function () {
//     $generalIssuer = new GeneralIssuer(
//         new Issuer(
//             'issuer@test.com',
//             '123456789',
//             'Comercial Name',
//             'Issuer Name'
//         ),
//         new GeneralAddress(new Address(
//             '14 avenida A',
//             '01006',
//             'Guatemala',
//             'Guatemala',
//             'Guatemala'
//         ), AddressType::Issuer),
//         IVAAffiliationType::General
//     );

//     echo 'General Issuer XML';
//     echo "\n";
//     echo $generalIssuer->asXML();
// });

// it('General Customer XML', function () {
//     $generalCustomer = new GeneralCustomer(
//         new Customer(
//             '123456789',
//             'customer@test.com',
//             'Customer Name',
//             'Special Type'
//         ),
//         new GeneralAddress(new Address(
//             '14 avenida A',
//             '01006',
//             'Guatemala',
//             'Guatemala',
//             'Guatemala'
//         ), AddressType::Customer)
//     );

//     echo 'General Customer XML';
//     echo "\n";
//     echo $generalCustomer->asXML();
// });

// it('Tax detail', function() {
//     $taxDetail = new TaxDetail(
//         1, //code
//         100.00, // taxable amount
//         null, // taxable unit count
//         12.00, // tax amount
//         12.00// total
//     );

//     $generalTaxDetail = new GeneralTaxDetail($taxDetail, TaxNames::IVA);

//     echo 'Tax detail';
//     echo "\n";
//     echo $generalTaxDetail->asXML();
// });

it('Items XML', function () {
    $item = new Item(
        '1',
        'B',
        1,
        'Unidad',
        'Producto de Prueba',
        100.00,
        100.00,
        0.00,
        100.00
    );

    $generalItems = new GeneralItems($item, ProductServiceType::Product, new GeneralTaxDetail(
        new TaxDetail(
            1, //code
            100.00, // taxable amount
            null, // taxable unit count
            12.00, // tax amount
            12.00// total
        ),
        TaxNames::IVA
    ));

    echo 'Items XML';
    echo "\n";
    echo $generalItems->asXML();
});
