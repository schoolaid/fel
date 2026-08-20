<?php

namespace Tests\Unit;

use DateTime;
use DateTimeZone;
use Schoolaid\Fel\Models\Cancellation;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Support\FelDateTime;

/**
 * La SAT fecha los DTE en hora de Guatemala (UTC-6, sin horario de verano).
 * Cuando el runtime corre en UTC —lo normal en un contenedor o con
 * APP_TIMEZONE=UTC— el default del paquete adelantaba el documento 6 horas:
 * una factura emitida a las 19:00 de Guatemala salía fechada al día
 * siguiente, y en notas de crédito eso descuadra
 * FechaEmisionDocumentoOrigen contra lo registrado en SAT.
 */
function withRuntimeTimezone(string $timezone, callable $callback): mixed
{
    $original = date_default_timezone_get();
    date_default_timezone_set($timezone);

    try {
        return $callback();
    } finally {
        date_default_timezone_set($original);
    }
}

/** Segundos entre la fecha del DTE (leída como hora de Guatemala) y ahora. */
function driftFromNow(string $value): int
{
    return abs((new DateTime($value, new DateTimeZone(FelDateTime::TIMEZONE)))->getTimestamp() - time());
}

it('stamps the default emission date in Guatemala time on a UTC runtime', function () {
    $invoice = withRuntimeTimezone('UTC', fn (): Invoice => new Invoice());

    expect($invoice->emissionDateTime)->toEndWith('-06:00')
        ->and(driftFromNow($invoice->emissionDateTime))->toBeLessThan(5);
});

it('stamps the default cancellation date in Guatemala time on a UTC runtime', function () {
    $cancellation = withRuntimeTimezone('UTC', fn (): Cancellation => new Cancellation(
        'DTE-UUID',
        '1170151K',
        'CF',
        'Devolución',
        '2026-08-19T11:26:54-06:00'
    ));

    // FechaHoraAnulacion viaja sin offset: se valida por el reloj de pared.
    expect(driftFromNow($cancellation->getCancellationDateTime()))->toBeLessThan(5);
});

it('keeps an explicit emission date untouched', function () {
    $invoice = new Invoice(emissionDateTime: '2025-04-10T20:19:55-06:00');

    expect($invoice->emissionDateTime)->toBe('2025-04-10T20:19:55-06:00');
});

it('exposes the Guatemala clock for callers that build their own dates', function () {
    $stamped = withRuntimeTimezone('UTC', fn (): string => FelDateTime::now());

    expect($stamped)->toEndWith('-06:00')
        ->and(driftFromNow($stamped))->toBeLessThan(5)
        ->and(FelDateTime::now('Y-m-d\TH:i:s'))->not->toContain('+');
});
