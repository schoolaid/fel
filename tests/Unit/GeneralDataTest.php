<?php

use SchoolAid\FEL\Documents\FELDocument;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Customer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELCustomer;
use SchoolAid\FEL\Documents\Bill\BillGeneralData;
use SchoolAid\FEL\Documents\Generator\GeneralBill;
use SchoolAid\FEL\Documents\Generic\FELAddendas;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Documents\Generic\FELItems;
use SchoolAid\FEL\Documents\Generic\FELPhrases;
use SchoolAid\FEL\Documents\Generic\FELTotals;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Models\Addenda;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\Phrase;

it('GeneralBill XML generator', function () {
    $generalData = new BillGeneralData();
    $generalIssuer = new FELIssuer(
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

    $phrases = [
        new Phrase(
            '1',
            '1',
            '1',
            '2021-01-01'
        ),
        new Phrase(
            '2',
            '2',
            '2',
            '2021-01-01'
        ),
    ];

    

    $documentFEL = new GeneralBill(
        $generalData,
        $generalIssuer,
        $generalCustomer,
        $Items,
        new FELPhrases($phrases),
        new FELAddendas($addendas),
        $Totals
    );

    echo 'GeneralBill XML generator';
    echo "\n";
    echo $documentFEL->generateXML();
            
});
