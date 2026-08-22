<?php

namespace Tests\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Config\FelConfig;

/**
 * Cuando INFILE rechaza un DTE, `descripcion` trae siempre el mismo texto
 * genérico ("Existen errores en la validacion del XML...") y el detalle real
 * viaja en `descripcion_errores`. La respuesta del paquete debe exponer ese
 * detalle: sin él, la app consumidora no puede decir por qué la SAT rechazó
 * (p. ej. una NCRE con receptor distinto al del documento origen).
 */
function infileErrorMappingConfig(MockHandler $mock, string $endpoint): FelConfig
{
    return new FelConfig('infile', 'usuario_demo', 'LLAVE_A', 'LLAVE_B', [
        'base_url' => 'http://127.0.0.1:1/',
        'certify_url' => $endpoint,
        'cancel_url' => $endpoint,
        'client_config' => ['handler' => HandlerStack::create($mock)],
    ]);
}

/** Payload real del sandbox al rechazar una NCRE con receptor distinto. */
function infileErrorMappingPayload(): array
{
    return [
        'resultado' => false,
        'descripcion' => 'Existen errores en la validacion del XML. Por favor revisa e intenta de nuevo.',
        'cantidad_errores' => 2,
        'descripcion_errores' => [
            [
                'resultado' => false,
                'fuente' => 'FEL Reglas y Validaciones',
                'categoria' => 'Complemento 4: REFERENCIAS NOTA DE CRÉDITO Y DÉBITO',
                'numeral' => '3.5.1',
                'validacion' => '5',
                'mensaje_error' => 'FEL-GUI-51 | 3.5 | 3.5.1 | No. 5 | Error - El valor de la casilla ID del Receptor no coincide con el registrado en el Documento Origen.',
            ],
            [
                'resultado' => false,
                'fuente' => 'FEL Reglas y Validaciones',
                'categoria' => 'Complementos (XSD:Complementos)',
                'numeral' => '3.1',
                'validacion' => '31101',
                'mensaje_error' => 'FEL-GUI-83 | 3 | 3.1 | No. 31101 | Error - El complemento [ReferenciasNota] con prefijo [cno] no es valido para el tipo de documento [FACT]. (31101)',
            ],
        ],
    ];
}

it('surfaces every SAT validation error when certification fails', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode(infileErrorMappingPayload())),
    ]);

    $response = (new InfileProvider(infileErrorMappingConfig($mock, 'certify')))->certify('<dte/>');

    expect($response->isSuccessful())->toBeFalse()
        ->and($response->getErrors())->toHaveCount(2)
        ->and($response->getErrors()[0])->toContain('ID del Receptor no coincide')
        ->and($response->getErrors()[1])->toContain('31101');
});

it('surfaces every SAT validation error when cancellation fails', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'resultado' => false,
            'descripcion' => 'Existen errores en la validacion del XML. Por favor revisa e intenta de nuevo.',
            'descripcion_errores' => [
                [
                    'mensaje_error' => 'FEL-GUI-XX | 3.13 | Error - El documento tiene una nota de crédito vigente asociada.',
                ],
            ],
        ])),
    ]);

    $response = (new InfileProvider(infileErrorMappingConfig($mock, 'cancel')))->cancel('<anulacion/>');

    expect($response->isSuccessful())->toBeFalse()
        ->and($response->getErrors())->toHaveCount(1)
        ->and($response->getErrors()[0])->toContain('nota de crédito vigente');
});

it('falls back to descripcion when there is no error detail', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'resultado' => false,
            'descripcion' => 'Credenciales invalidas',
        ])),
    ]);

    $response = (new InfileProvider(infileErrorMappingConfig($mock, 'certify')))->certify('<dte/>');

    expect($response->getErrors())->toBe(['Credenciales invalidas']);
});

it('accepts descripcion_errores as a list of plain strings', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'resultado' => false,
            'descripcion' => 'Existen errores en la validacion del XML.',
            'descripcion_errores' => ['Error simple del certificador'],
        ])),
    ]);

    $response = (new InfileProvider(infileErrorMappingConfig($mock, 'certify')))->certify('<dte/>');

    expect($response->getErrors())->toBe(['Error simple del certificador']);
});
