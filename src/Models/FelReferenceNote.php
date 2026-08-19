<?php

namespace Schoolaid\Fel\Models;

use DateTimeInterface;

/**
 * Referencia al documento origen para notas de crédito (NCRE) y débito (NDEB).
 * Se serializa como el complemento SAT "ReferenciasNota"
 * (http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0).
 */
class FelReferenceNote
{
    public const OLD_REGIME_VALUE = 'Antiguo';

    public string $originDocumentAuthorizationNumber;
    public string $originDocumentEmissionDate;
    public string $adjustmentReason;
    public string $originDocumentSeries;
    public string $originDocumentNumber;
    public bool $oldRegime;
    public string $version;

    /**
     * Serie y número del documento origen son requeridos por el XSD del
     * complemento; para un DTE del régimen FEL se toman de la respuesta de
     * certificación del documento origen (no se derivan del UUID: el sandbox
     * de INFILE, por ejemplo, emite la serie "**PRUEBAS**").
     */
    public function __construct(
        string $originDocumentAuthorizationNumber,
        DateTimeInterface|string $originDocumentEmissionDate,
        string $adjustmentReason,
        string $originDocumentSeries,
        string $originDocumentNumber,
        bool $oldRegime = false,
        string $version = '1'
    ) {
        $this->originDocumentAuthorizationNumber = $originDocumentAuthorizationNumber;
        $this->originDocumentEmissionDate = $originDocumentEmissionDate instanceof DateTimeInterface
            ? $originDocumentEmissionDate->format('Y-m-d')
            : $originDocumentEmissionDate;
        $this->adjustmentReason = $adjustmentReason;
        $this->originDocumentSeries = $originDocumentSeries;
        $this->originDocumentNumber = $originDocumentNumber;
        $this->oldRegime = $oldRegime;
        $this->version = $version;
    }

    public function toArray(): array
    {
        return [
            'originDocumentAuthorizationNumber' => $this->originDocumentAuthorizationNumber,
            'originDocumentEmissionDate' => $this->originDocumentEmissionDate,
            'adjustmentReason' => $this->adjustmentReason,
            'originDocumentSeries' => $this->originDocumentSeries,
            'originDocumentNumber' => $this->originDocumentNumber,
            'oldRegime' => $this->oldRegime,
            'version' => $this->version,
        ];
    }
}
