<?php

use SchoolAid\FEL\Documents\Bill\General\BillGeneralData;
use SchoolAid\FEL\Documents\Generic\General\GeneralAddress;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Documents\Generic\General\GeneralIssuer;
use SchoolAid\FEL\Documents\Generic\General\GeneralCustomer;
use SchoolAid\FEL\Documents\Generic\General\GeneralItems;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Enum\General\IVAAffiliationType;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Models\Items;

it('Bill General Data generates the XML correctly', function () {
    $generalData = new BillGeneralData();

    echo $generalData->asXML();
});


it('General Issuer XML', function () {
    $generalIssuer = new GeneralIssuer(
        new Issuer(
            'issuer@test.com',
            '123456789',
            'Comercial Name',
            'Issuer Name'
        ),
        new GeneralAddress(new Address(
            '14 avenida A',
            '01006',
            'Guatemala',
            'Guatemala',
            'Guatemala'
        ), AddressType::Issuer),
        IVAAffiliationType::General
    );

    echo 'General Issuer XML';
    echo "\n";
    echo $generalIssuer->asXML();
});

it('General Customer XML', function () {
    $generalCustomer = new GeneralCustomer(
        new Customer(
            '123456789',
            'customer@test.com',
            'Customer Name',
            'Special Type'
        ),
        new GeneralAddress(new Address(
            '14 avenida A',
            '01006',
            'Guatemala',
            'Guatemala',
            'Guatemala'
        ), AddressType::Customer)
    );

    echo 'General Customer XML';
    echo "\n";
    echo $generalCustomer->asXML();
});


it('Items XML', function () {
    $items = new Items(
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

    $generalItems = new GeneralItems($items);

    echo 'Items XML';
    echo "\n";
    echo $generalItems->asXML();
});
