<?php

namespace Tests\Unit;

use DateTime;
use Schoolaid\Fel\Actions\FelCancel;
use Schoolaid\Fel\Config\FelConfig;

it('correctly generates XML for document cancellation', function () {
    // 1. Create a test config with known values
    $config = FelConfig::fromConfig();

    // 2. Fixed dates for consistent testing

    $cancel = FelCancel::fromParams(
        'CEC23EF0-D54C-40E0-8750-E7D976163B4B',
        '11201169K',
        'CANCELACIÓN',
        $config,
        'CF',
        '2025-04-14T15:24:11-06:00',
        '2025-04-14T15:24:15-06:00'
    );

    // 4. Generate cancellation XML
    $xml = $cancel->generateXml();

    // 5. Validate XML structure and content
    expect($xml)->toBeString()
        ->and($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
        ->and($xml)->toContain('<dte:GTAnulacionDocumento')
        ->and($xml)->toContain('xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0"')
        ->and($xml)->toContain('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"')
        ->and($xml)->toContain('Version="0.1"')
        ->and($xml)->toContain('<dte:SAT>')
        ->and($xml)->toContain('<dte:AnulacionDTE ID="DatosCertificados">')
        ->and($xml)->toContain('<dte:DatosGenerales')
        ->and($xml)->toContain('ID="DatosAnulacion"')
        ->and($xml)->toContain('NITEmisor="11201169K"')
        ->and($xml)->toContain('IDReceptor="CF"')
        ->and($xml)->toContain('NumeroDocumentoAAnular="CEC23EF0-D54C-40E0-8750-E7D976163B4B"')
        ->and($xml)->toContain('MotivoAnulacion="CANCELACIÓN"')
        ->and($xml)->toContain('FechaHoraAnulacion="2025-04-14T15:24:15-06:00"')
        ->and($xml)->toContain('FechaEmisionDocumentoAnular="2025-04-14T15:24:11-06:00"');
});