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
 * Matriz de escenarios en vivo para notas de crédito/débito contra el sandbox
 * de INFILE. Cada documento (aceptado o rechazado) queda en tests/logs/ y el
 * resumen de la corrida en tests/logs/RESUMEN-<fecha>.md.
 *
 * El emisor sale de las variables FEL_ISSUER_* del .env (una fila de
 * bill_issuers) vía felTestIssuer(); la fecha de emisión, de
 * felEmissionDateTime() (hora de Guatemala, igual que el default del
 * paquete).
 */

/** FelReceiver: (id, email, name, address) */
function noteScenarioReceiver(string $id = 'CF', string $name = 'Consumidor Final'): FelReceiver
{
    return new FelReceiver(
        $id,
        'es.evitra@gmail.com',
        $name,
        new FelAddress('Ciudad', '01011', 'Guatemala', 'Guatemala', 'GT')
    );
}

/**
 * @param array<int, array{desc: string, amount: float}> $lines
 */
function noteScenarioInvoice(
    DocumentTypeEnum $type,
    ?string $emissionDateTime,
    array $lines,
    ?FelReceiver $receiver = null
): Invoice {
    $items = [];
    foreach (array_values($lines) as $index => $line) {
        $items[] = new FelItem(
            $index + 1,
            'S',
            $line['amount'],
            'UND',
            $line['desc'],
            $line['amount'],
            1,
            0,
            [],
            $line['amount']
        );
    }

    return new Invoice(
        $type,
        $emissionDateTime,
        CurrencyEnum::QUETZAL,
        felTestIssuer(),
        $receiver ?? noteScenarioReceiver(),
        felTestIssuerPhrases(),
        new FelItems($items),
        new FelTotals(grandTotal: array_sum(array_column($lines, 'amount'))),
        null,
        null,
        felTestIssuerData()['personType']
    );
}

/**
 * Certifica y deja el rastro completo en tests/logs/. Devuelve la respuesta y
 * la fila del resumen.
 *
 * @param array<string, mixed> $extraMeta
 */
function noteScenarioCertify(string $name, string $description, Invoice $invoice, array $extraMeta = []): array
{
    $config = FelConfig::fromConfig();
    $config->setIdentifier(Str::uuid()->toString());

    $error = null;
    $response = null;

    try {
        $response = (new FelCertify($invoice, $config))->execute();
    } catch (\Throwable $e) {
        $error = get_class($e) . ': ' . $e->getMessage();
    }

    $raw = $response?->getRawResponse();
    $satErrors = is_array($raw) ? ($raw['descripcion_errores'] ?? []) : [];

    $meta = array_merge([
        'scenario' => $name,
        'description' => $description,
        'documentType' => $invoice->documentType,
        'expected' => $extraMeta['expected'] ?? null,
        'successful' => $response?->isSuccessful() ?? false,
        'uuid' => $response?->getUuid(),
        'series' => $response?->getSeries(),
        'number' => $response?->getNumber(),
        'certificationDate' => $response?->getCertificationDate(),
        'grandTotal' => $invoice->totals->grandTotal,
        'issuerNit' => $invoice->issuer->nit,
        'receiverId' => $invoice->receiver->id,
        'referenceNote' => $invoice->getReferenceNote()?->toArray(),
        'exception' => $error,
        'errors' => $response?->getErrors() ?? [],
        'satErrors' => $satErrors,
        'rawResponse' => $raw,
    ], $extraMeta);

    $dir = felLogDocument($name, $meta, [
        'request.xml' => $response?->getRequestXml(),
        'certified.xml' => $response?->getCertifiedXml(),
    ]);

    return [
        'response' => $response,
        'row' => [
            'scenario' => $name,
            'description' => $description,
            'type' => $invoice->documentType,
            'total' => $invoice->totals->grandTotal,
            'expected' => $extraMeta['expected'] ?? '',
            'successful' => $response?->isSuccessful() ?? false,
            'uuid' => $response?->getUuid(),
            'errors' => $response?->getErrors() ?? [],
            'satErrors' => $satErrors,
            'exception' => $error,
            'dir' => basename($dir),
        ],
    ];
}

/**
 * @param array<int, array<string, mixed>> $rows
 */
function noteScenarioWriteSummary(array $rows): string
{
    $path = __DIR__ . '/../logs/RESUMEN-' . date('Ymd-His') . '.md';

    $lines = [
        '# Corrida de escenarios NCRE/NDEB — ' . date('c'),
        '',
        'Sandbox de INFILE. Un directorio por documento en `tests/logs/`.',
        'Emisor: NIT ' . felTestIssuerData()['nit'] . ' — ' . felTestIssuerData()['name'] . '.',
        '',
        '| # | Escenario | Tipo | Total | Esperado | Resultado | UUID | Errores SAT/INFILE |',
        '|---|---|---|---|---|---|---|---|',
    ];

    foreach ($rows as $i => $row) {
        $satErrors = array_map(
            fn ($e) => is_array($e) ? json_encode($e, JSON_UNESCAPED_UNICODE) : (string) $e,
            (array) ($row['satErrors'] ?: $row['errors'])
        );

        $lines[] = sprintf(
            '| %d | %s<br><sub>%s</sub> | %s | %s | %s | %s | %s | %s |',
            $i + 1,
            $row['scenario'],
            $row['description'],
            $row['type'],
            number_format((float) $row['total'], 2),
            $row['expected'],
            $row['successful'] ? 'certificado' : 'rechazado',
            $row['uuid'] ?? '—',
            $satErrors ? str_replace('|', '/', implode('<br>', $satErrors)) : '—'
        );
    }

    $lines[] = '';
    $lines[] = '## Directorios';
    $lines[] = '';
    foreach ($rows as $row) {
        $lines[] = sprintf('- `%s` — %s', $row['dir'], $row['description']);
    }
    $lines[] = '';

    file_put_contents($path, implode("\n", $lines));

    return $path;
}

it('runs the live NCRE/NDEB scenario matrix against the INFILE sandbox', function () {
    $rows = [];
    $emission = felEmissionDateTime();
    $emissionDate = substr($emission, 0, 10);

    // 1. Factura origen (FACT) de Q100 en dos líneas.
    $fact = noteScenarioInvoice(DocumentTypeEnum::LOCAL_INVOICE, $emission, [
        ['desc' => 'Colegiatura agosto', 'amount' => 75.00],
        ['desc' => 'Material didáctico', 'amount' => 25.00],
    ]);
    $factResult = noteScenarioCertify(
        'fact-origen',
        'Factura normal de Q100 que servirá de documento origen',
        $fact,
        ['expected' => 'certificado']
    );
    $rows[] = $factResult['row'];
    $factResponse = $factResult['response'];

    expect($factResponse?->isSuccessful())->toBeTrue(
        'La factura origen debe certificarse: ' . json_encode($factResult['row']['satErrors'], JSON_UNESCAPED_UNICODE)
    );

    $originUuid = $factResponse->getUuid();
    $originSeries = $factResponse->getSeries();
    $originNumber = (string) $factResponse->getNumber();

    $reference = fn (string $reason): FelReferenceNote => new FelReferenceNote(
        $originUuid,
        $emissionDate,
        $reason,
        $originSeries,
        $originNumber
    );

    // 2. NCRE parcial (Q25): devolución de una sola línea del origen.
    $partial = noteScenarioInvoice(DocumentTypeEnum::CREDIT_NOTE, felEmissionDateTime(), [
        ['desc' => 'Devolución material didáctico', 'amount' => 25.00],
    ]);
    $partial->setReferenceNote($reference('DEVOLUCION PARCIAL DE MATERIAL'));
    $partialResult = noteScenarioCertify(
        'ncre-parcial',
        'NCRE de Q25 sobre la factura origen (ajuste parcial)',
        $partial,
        ['expected' => 'certificado', 'originUuid' => $originUuid]
    );
    $rows[] = $partialResult['row'];

    // 3. Segunda NCRE (Q75) sobre el mismo origen: ¿acepta SAT varias notas?
    $rest = noteScenarioInvoice(DocumentTypeEnum::CREDIT_NOTE, felEmissionDateTime(), [
        ['desc' => 'Devolución colegiatura agosto', 'amount' => 75.00],
    ]);
    $rest->setReferenceNote($reference('DEVOLUCION DEL SALDO'));
    $restResult = noteScenarioCertify(
        'ncre-segunda',
        'Segunda NCRE (Q75) sobre la misma factura origen',
        $rest,
        ['expected' => 'certificado', 'originUuid' => $originUuid]
    );
    $rows[] = $restResult['row'];

    // 4. NDEB sobre el mismo origen (nunca verificada en vivo hasta ahora).
    $debit = noteScenarioInvoice(DocumentTypeEnum::DEBIT_NOTE, felEmissionDateTime(), [
        ['desc' => 'Cargo por mora', 'amount' => 15.00],
    ]);
    $debit->setReferenceNote($reference('CARGO POR MORA'));
    $debitResult = noteScenarioCertify(
        'ndeb',
        'NDEB de Q15 sobre la misma factura origen',
        $debit,
        ['expected' => 'certificado', 'originUuid' => $originUuid]
    );
    $rows[] = $debitResult['row'];

    // 5. Control negativo: UUID de origen inexistente.
    $ghost = noteScenarioInvoice(DocumentTypeEnum::CREDIT_NOTE, felEmissionDateTime(), [
        ['desc' => 'Devolución contra documento inexistente', 'amount' => 10.00],
    ]);
    $ghost->setReferenceNote(new FelReferenceNote(
        '00000000-0000-4000-8000-000000000000',
        $emissionDate,
        'PRUEBA UUID INEXISTENTE',
        $originSeries,
        $originNumber
    ));
    $ghostResult = noteScenarioCertify(
        'ncre-uuid-inexistente',
        'Control negativo: NCRE contra un UUID que no existe en SAT',
        $ghost,
        ['expected' => 'rechazado']
    );
    $rows[] = $ghostResult['row'];

    // 6. Control negativo: receptor distinto al del documento origen (regla 3.5).
    $otherReceiver = noteScenarioInvoice(
        DocumentTypeEnum::CREDIT_NOTE,
        felEmissionDateTime(),
        [['desc' => 'Devolución con receptor distinto', 'amount' => 10.00]],
        noteScenarioReceiver('73023094', 'DANCE STUDIO, SOCIEDAD ANONIMA')
    );
    $otherReceiver->setReferenceNote($reference('PRUEBA RECEPTOR DISTINTO'));
    $otherReceiverResult = noteScenarioCertify(
        'ncre-receptor-distinto',
        'Control negativo: NCRE cuyo receptor no coincide con el del origen',
        $otherReceiver,
        ['expected' => 'rechazado', 'originUuid' => $originUuid]
    );
    $rows[] = $otherReceiverResult['row'];

    // 7. Control negativo: FACT con complemento ReferenciasNota (prohibido
    //    por el catálogo de complementos, error 31101 de la SAT). El paquete
    //    lo corta en la generación del XML, sin gastar la llamada a INFILE.
    $factWithComplement = noteScenarioInvoice(DocumentTypeEnum::LOCAL_INVOICE, felEmissionDateTime(), [
        ['desc' => 'Factura con complemento indebido', 'amount' => 10.00],
    ]);
    $factWithComplement->setReferenceNote($reference('COMPLEMENTO EN FACT'));
    $factWithComplementResult = noteScenarioCertify(
        'fact-con-complemento',
        'Control negativo: FACT con complemento ReferenciasNota (el paquete lo corta local)',
        $factWithComplement,
        ['expected' => 'rechazado']
    );
    $rows[] = $factWithComplementResult['row'];

    // 8. La fecha por defecto (sin emissionDateTime) debe salir en hora de
    //    Guatemala y ser aceptada por INFILE, corra donde corra el runtime.
    $defaultDate = noteScenarioInvoice(DocumentTypeEnum::LOCAL_INVOICE, null, [
        ['desc' => 'Factura con fecha por defecto', 'amount' => 10.00],
    ]);
    $defaultDateResult = noteScenarioCertify(
        'fact-fecha-default',
        'FACT sin emissionDateTime: sella la fecha en America/Guatemala',
        $defaultDate,
        ['expected' => 'certificado', 'emissionDateTime' => $defaultDate->emissionDateTime]
    );
    $rows[] = $defaultDateResult['row'];

    $summary = noteScenarioWriteSummary($rows);
    fwrite(STDERR, "\n\nResumen de la corrida: {$summary}\n");
    foreach ($rows as $row) {
        fwrite(STDERR, sprintf(
            "  [%s] %-24s %s  %s\n",
            $row['successful'] ? 'OK ' : 'ERR',
            $row['scenario'],
            $row['uuid'] ?? '-',
            $row['satErrors'] ? json_encode($row['satErrors'], JSON_UNESCAPED_UNICODE) : ''
        ));
    }

    // Contratos verificados en vivo contra el sandbox.
    expect($partialResult['response']?->isSuccessful())->toBeTrue(
        'NCRE parcial: ' . json_encode($partialResult['row']['satErrors'], JSON_UNESCAPED_UNICODE)
    )
        ->and($restResult['response']?->isSuccessful())->toBeTrue(
            'Segunda NCRE sobre el mismo origen: ' . json_encode($restResult['row']['satErrors'], JSON_UNESCAPED_UNICODE)
        )
        ->and($debitResult['response']?->isSuccessful())->toBeTrue(
            'NDEB: ' . json_encode($debitResult['row']['satErrors'], JSON_UNESCAPED_UNICODE)
        )
        // La SAT rechaza la referencia a un DTE inexistente y el receptor que
        // no coincide con el del documento origen (regla 3.5.1).
        ->and($ghostResult['response']?->isSuccessful())->toBeFalse()
        ->and($otherReceiverResult['response']?->isSuccessful())->toBeFalse()
        // Y el complemento en una FACT ni siquiera sale del paquete.
        ->and($factWithComplementResult['row']['exception'])
            ->toContain('XmlGenerationException')
        // La fecha por defecto sale en hora de Guatemala y la SAT la acepta.
        ->and($defaultDate->emissionDateTime)->toEndWith('-06:00')
        ->and($defaultDateResult['response']?->isSuccessful())->toBeTrue(
            'FACT con fecha por defecto: ' . json_encode($defaultDateResult['row']['satErrors'], JSON_UNESCAPED_UNICODE)
        );
})->group('integration')->skip(
    fn (): bool => ! felHasLiveCredentials(),
    'Requiere credenciales de testing de INFILE en el .env'
);
