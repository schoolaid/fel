<?php

namespace Schoolaid\Fel\Support;

use DateTime;
use DateTimeZone;

/**
 * Reloj del régimen FEL.
 *
 * La SAT fecha los DTE en hora de Guatemala (UTC-6 fijo, sin horario de
 * verano). El paquete no puede depender de la zona del runtime: con
 * APP_TIMEZONE=UTC —lo normal en contenedores— un documento emitido después
 * de las 18:00 locales saldría fechado al día siguiente, con lo que eso
 * implica para el período fiscal y para el `FechaEmisionDocumentoOrigen` de
 * una nota de crédito.
 */
class FelDateTime
{
    public const TIMEZONE = 'America/Guatemala';

    public static function timezone(): DateTimeZone
    {
        return new DateTimeZone(self::TIMEZONE);
    }

    /**
     * Ahora, en hora de Guatemala. El formato por defecto ('c') incluye el
     * offset -06:00; para los campos que viajan sin offset se pasa
     * 'Y-m-d\TH:i:s'.
     */
    public static function now(string $format = 'c'): string
    {
        return (new DateTime('now', self::timezone()))->format($format);
    }
}
