<?php

namespace Tests\Unit;

use Schoolaid\Fel\Actions\FelCertify;
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

it('can create a valid invoice object for certification', function () {
    // Crear una factura igual a la del test de generación XML
    $invoice = createTestInvoice();
    // Verificar que la factura sea válida
    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->documentType)->toBe(DocumentTypeEnum::LOCAL_INVOICE->value)
        ->and($invoice->currencyCode)->toBe(CurrencyEnum::QUETZAL->value)
        ->and($invoice->issuer->nit)->toBe('11201169K')
        ->and($invoice->items->items)->toHaveCount(1);
});

it( 'can certify an invoice with the FEL service', function () {
    // Crear la factura para certificar
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
    // En un caso real, deberíamos verificar que la certificación fue exitosa,
    // pero como esto depende de credenciales válidas, solo verificamos que el
    // proceso no falló con una excepción
});

/**
 * Crear una factura de prueba para los tests
 * 
 * @return Invoice
 */
function createTestInvoice(): Invoice
{
    // 1. Create issuer address
    $issuerAddress = new FelAddress(
        '15 AVENIDA 5-50 COLONIA VISTA HERMOSA III, EDIFICIO SPAZIO NIVEL 2 OF. 209 ZONA 15',
        '10101',
        'Villa Nueva',
        'Guatemala',
        'GT',
    );

    // 2. Create issuer
    $issuer = new FelIssuer(
        'esevitra@gmail.com',
        '1',
        '11201169K',
        'Demo',
        IVAAffiliationTypeEnum::General,
        'Laid Demo',
        $issuerAddress
    );

    // 3. Create a receiver address
    $receiverAddress = new FelAddress(
        'Villa Nueva',
        '01064',
        'Villa Nueva',
        'Guatemala',
        'GT',
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

    // 6. Create item without specific taxes to test automatic calculation
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

    // 9. Create multiple addendas
    $addendas = [
        new FelAddenda(
            'http://www.sat.gob.gt/face2/ComplementoFiscal',
            'Orden',
            'Orden #186, Arellano Sanchinelli, Renata - abril 2025'
        ),
        new FelAddenda(
            'http://www.sat.gob.gt/face2/InfoAdicional',
            'InfoAdicional',
            'Información adicional para la factura'
        )
    ];

    // 10. Create invoice using enums directly
    return new Invoice(
        DocumentTypeEnum::LOCAL_INVOICE,
        now()->format('Y-m-d\TH:i:s'),
        CurrencyEnum::QUETZAL,
        $issuer,
        $receiver,
        $phrases,
        $items,
        $totals,
        $addendas
    );
} 