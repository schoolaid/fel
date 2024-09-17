<?php

use SchoolAid\FEL\Documents\Bill\General\BillGeneralData;

it('Bill General Data generates the XML correctly', function () {
    $generalData = new BillGeneralData();
    //TODO: Validar la estructura del XML
    echo ($generalData->asXML());
});
