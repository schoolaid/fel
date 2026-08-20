<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Schoolaid\Fel\Actions\FelCertify;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelReferenceNote;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;

/**
 * Construye el documento base para el flujo vivo de nota de crédito.
 * El emisor y sus frases salen de las variables FEL_ISSUER_* del .env
 * (felTestIssuer / felTestIssuerPhrases); el tipo y la referencia al
 * documento origen los define cada test.
 */
function createNoteCertifyInvoice(DocumentTypeEnum $type, string $emissionDateTime): Invoice
{
    $receiverAddress = new FelAddress('Villa Nueva', '01064', 'Villa Nueva', 'Guatemala', 'GT');
    $receiver = new FelReceiver('CF', 'es.evitra@gmail.com', 'Consumidor Final', $receiverAddress);

    $items = new FelItems([
        new FelItem(1, 'S', 20.0, 'UND', '1 disciplina', 20, 1, 0, [], 20),
    ]);
    $totals = new FelTotals(grandTotal: 20);

    return new Invoice(
        $type,
        $emissionDateTime,
        CurrencyEnum::QUETZAL,
        felTestIssuer(),
        $receiver,
        felTestIssuerPhrases(),
        $items,
        $totals,
        null,
        null,
        felTestIssuerData()['personType']
    );
}

it('can certify a credit note referencing a freshly certified invoice', function () {
    $config = FelConfig::fromConfig();
    $emissionDateTime = felEmissionDateTime();

    // 1. Certificar la factura origen (la NCRE debe referenciar un DTE
    //    vigente del mismo emisor y receptor)
    $factInvoice = createNoteCertifyInvoice(DocumentTypeEnum::LOCAL_INVOICE, $emissionDateTime);
    $config->setIdentifier(Str::uuid()->toString());
    $factResponse = (new FelCertify($factInvoice, $config))->execute();

    felLogDocument('certify-fact-origen-ncre', [
        'test' => 'CreditNoteCertifyTest: factura origen para la NCRE',
        'documentType' => $factInvoice->documentType,
        'successful' => $factResponse->isSuccessful(),
        'uuid' => $factResponse->getUuid(),
        'series' => $factResponse->getSeries(),
        'number' => $factResponse->getNumber(),
        'certificationDate' => $factResponse->getCertificationDate(),
        'errors' => $factResponse->getErrors(),
        'rawResponse' => $factResponse->getRawResponse(),
    ], [
        'request.xml' => $factResponse->getRequestXml(),
        'certified.xml' => $factResponse->getCertifiedXml(),
    ]);

    expect($factResponse->isSuccessful())->toBeTrue();

    // 2. Certificar la nota de crédito que la referencia
    $creditNote = createNoteCertifyInvoice(
        DocumentTypeEnum::CREDIT_NOTE,
        felEmissionDateTime()
    );
    $creditNote->setReferenceNote(new FelReferenceNote(
        $factResponse->getUuid(),
        substr($emissionDateTime, 0, 10),
        'DEVOLUCION',
        $factResponse->getSeries(),
        (string) $factResponse->getNumber()
    ));

    $config->setIdentifier(Str::uuid()->toString());
    $noteResponse = (new FelCertify($creditNote, $config))->execute();

    felLogDocument('certify-ncre', [
        'test' => 'CreditNoteCertifyTest: certificación de la NCRE',
        'documentType' => $creditNote->documentType,
        'successful' => $noteResponse->isSuccessful(),
        'originUuid' => $factResponse->getUuid(),
        'uuid' => $noteResponse->getUuid(),
        'series' => $noteResponse->getSeries(),
        'number' => $noteResponse->getNumber(),
        'certificationDate' => $noteResponse->getCertificationDate(),
        'errors' => $noteResponse->getErrors(),
        'rawResponse' => $noteResponse->getRawResponse(),
    ], [
        'request.xml' => $noteResponse->getRequestXml(),
        'certified.xml' => $noteResponse->getCertifiedXml(),
    ]);

    expect($noteResponse->isSuccessful())->toBeTrue()
        ->and($noteResponse->getUuid())->not->toBeNull();
})->group('integration')->skip(
    fn (): bool => ! felHasLiveCredentials(),
    'Requiere credenciales de testing de INFILE en el .env'
);
