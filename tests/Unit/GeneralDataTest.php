<?php

use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELAddress;

it(' Issuer XML', function () {
    $Issuer = new FELIssuer(
        new Issuer(
            'issuer@test.com',
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

    echo ' Issuer XML';
    echo "\n";
    echo $Issuer->asXML();
});

it('Items XML', function () {

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

    echo 'Items XML';
    echo "\n";
    echo $Items->asXML();
});
