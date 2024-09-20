<?php

use SchoolAid\FEL\Documents\Bill\General\BillGeneralData;
use SchoolAid\FEL\Documents\Generic\General\GeneralAddress;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Documents\Generic\General\GeneralIssuer;


it('Bill General Data generates the XML correctly', function () {
    $generalData = new BillGeneralData();

    echo $generalData->asXML();
});

it('Generate ADDRESS headers XML', function() {
    $adress = new GeneralAddress(new Address(
        '14 avenida A',
        '01006',
        'Guatemala',
        'Guatemala',
        'Guatemala'
    ), AddressType::Issuer);
    echo 'Generate ADDRESS headers XML';
    echo "\n";
    echo $adress->asXML();
    
});

it('General Issuer XML', function() {
    $generalIssuer = new GeneralIssuer();
    echo $generalIssuer->asXML();
});