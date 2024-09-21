<?php

use SchoolAid\FEL\Documents\Bill\General\BillGeneralData;
use SchoolAid\FEL\Documents\Generic\General\GeneralAddress;
use SchoolAid\FEL\Enum\General\AddressType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Documents\Generic\General\GeneralIssuer;
use SchoolAid\FEL\Models\Issuer;
use SchoolAid\FEL\Enum\General\IVAAffiliationType;


it('Bill General Data generates the XML correctly', function () {
    $generalData = new BillGeneralData();

    echo $generalData->asXML();
});


it('General Issuer XML', function() {
    $generalIssuer = new GeneralIssuer(
        new Issuer(
            'issuer@test.com',
            '123456789',
            'Comercial Name',
            'Issuer Name'),
        new GeneralAddress( new Address(
            '14 avenida A',
            '01006',
            'Guatemala',
            'Guatemala',
            'Guatemala'
        ), AddressType::Issuer),
        IVAAffiliationType::General);

        echo 'General Issuer XML';
        echo "\n";
        echo $generalIssuer->asXML();
});