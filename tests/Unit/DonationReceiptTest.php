<?php

namespace Tests\Unit;

use Schoolaid\Fel\Actions\FelCertify;
use Schoolaid\Fel\Actions\FelGenerate;
use Schoolaid\Fel\Certification\Responses\CertificationResponse;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
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
use Illuminate\Support\Str;

it('correctly generates XML for donation receipt without taxes', function () {
    // 1. Create an issuer address
    $invoice = createTestInvoice();
    // 10. Generate XML using FelGenerate action
    $generator = new FelGenerate($invoice);
    $xml = $generator->generateXml();

    // 11. Verify the XML structure
    expect($xml)->toBeString()
        ->and($xml)->toContain('<dte:GTDocumento')
        ->and($xml)->toContain('Tipo="' . DocumentTypeEnum::DONATION_RECEIPT->value . '"')
        ->and($xml)->toContain('<dte:GranTotal>2</dte:GranTotal>')
        ->and($xml)->not->toContain('<dte:NombreCorto>IVA</dte:NombreCorto>')
        ->and($xml)->not->toContain('<dte:MontoImpuesto>');

    dd($xml);
    // 12. Verify that no taxes were generated

    // 13. Check that the TotalImpuestos element is empty or not present
});

it( 'can certify an invoice with the FEL service', function () {
    // 1. Create an issuer address
    $invoice = createTestInvoice();

    // Obtener la configuración FEL desde variables de entorno
    $config = FelConfig::fromConfig();
    $config->setIdentifier(Str::uuid()->toString());
    // Crear la acción de certificación
    $certify = new FelCertify($invoice, $config);

    // Ejecutar la certificación
    $response = $certify->execute();


    // Verificar la respuesta
    expect($response)->toBeInstanceOf(CertificationResponse::class);

    dd($response);

});


function createTestInvoice(): Invoice
{
    // 1. Create an issuer address
    $issuerAddress = new FelAddress(
        '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
        '01001',
        'GUATEMALA',
        'GUATEMALA',
        'GT'
    );

    // 2. Create issuer
    $issuer = new FelIssuer(
        'esevitra@gmail.com',
        '1',
        '68244703',
        'Demo',
        IVAAffiliationTypeEnum::NonTaxable,
        'Laid Demo',
        $issuerAddress
    );

    // 3. Create a receiver address
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
        'es.evitra@gmail.com',
        'Consumidor Final',
        $receiverAddress
    );

    // 5. Create phrases
    $phrase = new FelPhrase(4,4);
    $phrases = new FelPhrases([$phrase]);

    // 6. Create an item without specific taxes to test automatic calculation
    $item = new FelItem(
        1,              // NumeroLinea
        'S',            // BienOServicio
        2.0,          // PrecioUnitario
        'UND',          // UnidadMedida
        '1 disciplina', // Descripción
        2,            // Precio
        1,              // Cantidad
        0,               // Descuento
        [],
        2             // Total
    );

    // 7. Create a collection of items
    $items = new FelItems([$item]);

    // 8. Create totals (without values, they will be calculated)
    $totals = new FelTotals(grandTotal: 2);

    // 9. Create addenda
    $addenda = new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFiscal',
        'Orden',
        'Orden #186, Arellano Sanchinelli, Renata - abril 2025'
    );

    // 10. Create an invoice using enums directly
    return new Invoice(
        DocumentTypeEnum::DONATION_RECEIPT,
        now()->format('Y-m-d\TH:i:s-06:00'),
        CurrencyEnum::QUETZAL,
        $issuer,
        $receiver,
        $phrases,
        $items,
        $totals,
        $addenda
    );
}