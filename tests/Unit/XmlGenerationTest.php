<?php

namespace Tests\Unit;

use Schoolaid\Fel\Actions\FelGenerate;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Enums\TaxEnum;
use Schoolaid\Fel\Models\FelAddenda;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrase;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;

it('correctly generates XML for general invoice', function () {
    // 1. Create issuer address
    $issuerAddress = new FelAddress(
        '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
        '01001',
        'GUATEMALA',
        'GUATEMALA',
        'GT'
    );

    // 2. Create issuer
    $issuer = new FelIssuer(
        'e@gmai.com',
        '1',
        '73023094',
        'DANCE STUDIO',
        IVAAffiliationTypeEnum::General,
        'DANCE STUDIO, SOCIEDAD ANONIMA',
        $issuerAddress
    );

    // 3. Create receiver address
    $receiverAddress = new FelAddress(
        'Villa Nueva',
        '01064',
        'GUATEMALA',
        'GUATEMALA',
        'GT'
    );

    // 4. Create receiver
    $receiver = new FelReceiver(
        'CF',
        'Consumidor Final',
        'es.evitra@gmail.com',
        $receiverAddress
    );

    // 5. Create phrases
    $phrase = new FelPhrase(1, 1);
    $phrases = new FelPhrases([$phrase]);

    // 6. Create item without specific taxes to test automatic calculation
    $item = new FelItem(
        1,              // NumeroLinea
        'S',            // BienOServicio
        640.0,          // PrecioUnitario
        'UND',          // UnidadMedida
        '1 disciplina', // Descripción
        640,            // Precio
        1,              // Cantidad
        0,               // Descuento
        [],
        640             // Total
    );

    // 7. Create collection of items
    $items = new FelItems([$item]);

    // 9. Create totals (without values, they will be calculated)
    $totals = new FelTotals(grandTotal: 640);

    // 10. Create addenda
    $addenda = new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFiscal',
        'Orden',
        'Orden #186, Arellano Sanchinelli, Renata - abril 2025'
    );

    // 11. Create invoice using enums directly
    $invoice = new Invoice(
        DocumentTypeEnum::LOCAL_INVOICE,
        '2025-04-10T20:19:55-06:00',
        CurrencyEnum::QUETZAL,
        $issuer,
        $receiver,
        $phrases,
        $items,
        $totals,
        $addenda
    );

    // 12. Generate XML
    $xml = FelGenerate::make($invoice)->generateXml();

    expect($xml)->toBeString()
        ->and($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
        ->and($xml)->toContain('<dte:GTDocumento')
        ->and($xml)->toContain('<dte:SAT ClaseDocumento="dte">')
        ->and($xml)->toContain('<dte:DTE ID="DatosCertificados">')
        ->and($xml)->toContain('<dte:DatosEmision ID="DatosEmision">')
        ->and($xml)->toContain('<dte:DatosGenerales FechaHoraEmision="2025-04-10T20:19:55-06:00" CodigoMoneda="GTQ" Tipo="FACT"/>')
        ->and($xml)->toContain('<dte:Emisor')
        ->and($xml)->toContain('<dte:Receptor')
        ->and($xml)->toContain('<dte:Frases>')
        ->and($xml)->toContain('<dte:Frase CodigoEscenario="1" TipoFrase="1"/>')
        ->and($xml)->toContain('<dte:Items>')
        ->and($xml)->toContain('<dte:Item NumeroLinea="1" BienOServicio="S">')
        ->and($xml)->toContain('<dte:Descripcion>1 disciplina</dte:Descripcion>')
        ->and($xml)->toContain('<dte:Impuestos>')
        ->and($xml)->toContain('<dte:NombreCorto>IVA</dte:NombreCorto>')
        ->and($xml)->toContain('<dte:Totales>')
        ->and($xml)->toContain('<dte:GranTotal>640</dte:GranTotal>')
        ->and($xml)->toContain('<dte:Adenda>')
        ->and($xml)->toContain('<Orden>Orden #186, Arellano Sanchinelli, Renata - abril 2025</Orden>')
        ->and($item->taxes)->not->toBeEmpty()
        ->and($item->taxes[0]->shortName)->toBe(TaxEnum::IVA);
});