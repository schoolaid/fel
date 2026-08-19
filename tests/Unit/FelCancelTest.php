<?php

namespace Tests\Unit;

use Schoolaid\Fel\Actions\FelCancel;
use Schoolaid\Fel\Actions\FelCertify;
use Schoolaid\Fel\Certification\Responses\CancellationResponse;
use Schoolaid\Fel\Certification\Responses\CertificationResponse;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Models\Cancellation;
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
use Mockery;

afterEach(function () {
    Mockery::close();
});

it('can generate cancellation xml', function () {
    // 1. Arrange
    $uuid = '12345678-1234-1234-1234-123456789012';
    $issuerNit = '73023094';
    $reason = 'Error en datos';
    $documentDate = '2023-01-15T14:00:00';
    $cancellationDate = '2023-01-16T10:00:00';

    $config = FelConfig::fromConfig();

    $cancellation = new Cancellation(
        $uuid,
        $issuerNit,
        'CF',
        $reason,
        $documentDate,
        $cancellationDate
    );

    $felCancel = new FelCancel($cancellation, $config);

    // 2. Act
    $xml = $felCancel->generateXml();

    // 3. Assert
    expect($xml)->toBeString()
        ->and($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
        ->and($xml)->toContain('<dte:GTAnulacionDocumento')
        ->and($xml)->toContain($uuid)
        ->and($xml)->toContain($issuerNit)
        ->and($xml)->toContain($reason);
});

it('can execute cancellation', function () {
    $invoice = createCancellationTestInvoice();

    // Obtener la configuración FEL desde variables de entorno
    $config = FelConfig::fromConfig();
    $config->setIdentifier(Str::uuid()->toString());
    // Crear la acción de certificación
    $certify = new FelCertify($invoice, $config);

    // Ejecutar la certificación
    $responseInvoice = $certify->execute();
    // Verificar la respuesta
    expect($responseInvoice)->toBeInstanceOf(CertificationResponse::class);

    felLogDocument('certify-for-cancel', [
        'test' => 'FelCancelTest: can execute cancellation (certificación previa)',
        'successful' => $responseInvoice->isSuccessful(),
        'uuid' => $responseInvoice->getUuid(),
        'series' => $responseInvoice->getSeries(),
        'number' => $responseInvoice->getNumber(),
        'certificationDate' => $responseInvoice->getCertificationDate(),
        'errors' => $responseInvoice->getErrors(),
        'rawResponse' => $responseInvoice->getRawResponse(),
    ], [
        'request.xml' => $responseInvoice->getRequestXml(),
        'certified.xml' => $responseInvoice->getCertifiedXml(),
    ]);

    expect($responseInvoice->isSuccessful())->toBeTrue();

    // 2. Fixed dates for consistent testing
    $cancellationDate = now()->format('Y-m-d\TH:i:s-06:00');

    $cancel = FelCancel::fromParams(
        $responseInvoice->getUuid(),
        $invoice->issuer->nit,
        'CANCELACIÓN',
        $config,
        $invoice->receiver->id,
        $invoice->emissionDateTime,
        $cancellationDate
    );

    $response = $cancel->execute();

    felLogDocument('cancel', [
        'test' => 'FelCancelTest: can execute cancellation',
        'successful' => $response->isSuccessful(),
        'uuid' => $responseInvoice->getUuid(),
        'cancellationDate' => $response->getCancellationDate()?->format(DATE_ATOM),
        'errors' => $response->getErrors(),
        'rawResponse' => $response->getRawResponse(),
    ], [
        'request.xml' => $response->getRequestXml(),
    ]);

    // 3. Assert
    expect($response)->toBeInstanceOf(CancellationResponse::class)
        ->and($response->isSuccessful())->toBeTrue();
})->group('integration')->skip(
    fn (): bool => ! felHasLiveCredentials(),
    'Requiere credenciales de testing de INFILE en el .env'
);

it('can be created from params', function () {
    // 1. Arrange
    $uuid = '12345678-1234-1234-1234-123456789012';
    $issuerNit = '73023094';
    $reason = 'Error en datos';

    $config = FelConfig::fromConfig();

    // 2. Act
    $felCancel = FelCancel::fromParams(
        $uuid,
        $issuerNit,
        $reason,
        $config
    );

    // 3. Assert
    expect($felCancel)->toBeInstanceOf(FelCancel::class);

    $cancellation = $felCancel->getCancellation();
    expect($cancellation)->toBeInstanceOf(Cancellation::class)
        ->and($cancellation->getDocumentUuid())->toBe($uuid)
        ->and($cancellation->getNitIssuer())->toBe($issuerNit)
        ->and($cancellation->getIdReceiver())->toBe('CF')
        ->and($cancellation->getReason())->toBe($reason);
});

function createCancellationTestInvoice(): Invoice
{
    // 1. Create an issuer address
    $issuerAddress = new FelAddress(
        '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
        '01001',
        'GUATEMALA',
        'GUATEMALA',
        'GT'
    );

    // 2. Create issuer (el NIT debe coincidir con las credenciales de testing)
    $issuer = new FelIssuer(
        'esevitra@gmail.com',
        '1',
        felTestIssuerNit(),
        'Demo',
        IVAAffiliationTypeEnum::General,
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
    $phrase = new FelPhrase(1, 1);
    $phrases = new FelPhrases([$phrase]);

    // 6. Create an item without specific taxes to test automatic calculation
    $item = new FelItem(
        1,              // NumeroLinea
        'S',            // BienOServicio
        20.0,          // PrecioUnitario
        'UND',          // UnidadMedida
        '1 disciplina', // Descripción
        20,            // Precio
        1,              // Cantidad
        0,               // Descuento
        [],
        20             // Total
    );

    // 7. Create collection of items
    $items = new FelItems([$item]);

    // 8. Create totals (without values, they will be calculated)
    $totals = new FelTotals(grandTotal: 20);

    // 9. Create addenda
    $addenda = new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFiscal',
        'Orden',
        'Orden #186, Arellano Sanchinelli, Renata - abril 2025'
    );

    // 10. Create an invoice using enums directly
    return new Invoice(
        DocumentTypeEnum::LOCAL_INVOICE,
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