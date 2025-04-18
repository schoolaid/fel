<?php

namespace Tests\Unit;

use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Enums\TaxEnum;
use Schoolaid\Fel\Models\FelAddenda;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelTax;
use Schoolaid\Fel\Models\FelTaxTotal;
use Schoolaid\Fel\Models\FelTotals;

/**
 * Test to validate the data structure based on a FEL XML
 */
it('correctly builds and stores data structure from FEL XML', function () {
    // Create data structure based on FEL XML

    // 1. Issuer
    $issuer = new FelIssuer(
        'e@gmai',
        '1',
        '73023094',
        'DANCE STUDIO',
        IVAAffiliationTypeEnum::General,
        'DANCE STUDIO',
        new FelAddress(
            '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
            '01001',
            'GUATEMALA',
            'GUATEMALA',
            'GT'
        )
    );

    // 2. Receiver
    $receiver = new FelReceiver(
        'CF',
        'Demo',
        'es.evitra@gmail.com',
        new FelAddress(
            '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
            '01001',
            'GUATEMALA',
            'GUATEMALA',
            'GT'
        )
    );

    // 3. Tax of FelItem
    $tax = new FelTax(
        TaxEnum::IVA,
        640
    );

    $tax->calculate();

    // 4. FelItem
    $item = new FelItem(
        1,              // NumeroLinea
        'S',            // BienOServicio
        1.0,            // PrecioUnitario
        'UND',
        'Servicio demo', // Descripción
        640,            // Precio
        640,            // Cantidad
        0,          // UnidadMedida
        [],
        640
    );

    // 5. Collection of FelItems
    $items = new FelItems([$item]);

    // 6. Total of taxes
    $taxTotal = new FelTaxTotal(
        [$tax]
    );
    $taxTotal->calculate();
    // 7. Totals
    $totals = new FelTotals(
        $taxTotal,
        640
    );

    // 8. FelAddenda (optional, additional data)
    $addenda = new FelAddenda(
        namespace: 'http://www.sat.gob.gt/face2/ComplementoFiscal',
        name: 'Orden',
        value: 'Orden de compra'
    );

    // Build the final array
    $felData = [
        'issuer' => $issuer->toArray(),
        'receiver' => $receiver->toArray(),
        'items' => $items->toArray(),
        'totals' => $totals->toArray(),
        'addenda' => $addenda->toArray(),
    ];

    // Verify the general structure

    // Show the complete structure for verification
    dd($felData);
});