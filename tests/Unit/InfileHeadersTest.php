<?php

namespace Tests\Unit;

use ReflectionMethod;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Config\FelConfig;

function makeInfileHeadersConfig(): FelConfig
{
    return new FelConfig(
        'infile',
        'usuario_demo',
        'VALOR_EN_API_KEY',
        'VALOR_EN_SIGNATURE_KEY',
        [
            'base_url' => 'https://example.test/',
            'certify_url' => 'certify',
            'status_url' => 'status',
            'cancel_url' => 'cancel',
        ]
    );
}

function infileHeadersFor(FelConfig $config): array
{
    $provider = new InfileProvider($config);
    $method = new ReflectionMethod($provider, 'getCommonHeaders');

    return $method->invoke($provider);
}

// ATENCIÓN: el mapeo apiKey -> llaveFirma y signatureKey -> llaveApi está
// INVERTIDO respecto a los nombres, y es el contrato vigente: los consumidores
// existentes (algunos fuera de nuestro control) ya pasan las llaves
// intercambiadas para compensar. Este test protege ese contrato: si falla
// porque alguien "corrigió" el cruce, se rompe la certificación de todos los
// consumidores actuales. No cambiar sin coordinar con todos ellos.
it('keeps the historically inverted credential mapping (llaveFirma <- apiKey, llaveApi <- signatureKey)', function () {
    $headers = infileHeadersFor(makeInfileHeadersConfig());

    expect($headers['Usuario'])->toBe('usuario_demo')
        ->and($headers['UsuarioFirma'])->toBe('usuario_demo')
        ->and($headers['UsuarioApi'])->toBe('usuario_demo')
        ->and($headers['llaveFirma'])->toBe('VALOR_EN_API_KEY')
        ->and($headers['llaveApi'])->toBe('VALOR_EN_SIGNATURE_KEY')
        ->and($headers['llave'])->toBe('VALOR_EN_SIGNATURE_KEY');
});

// Los accesores veraces exponen los slots históricos con su nombre real:
// la llave del firmador vive en apiKey y la del API REST en signatureKey.
it('exposes the inverted slots through truthful accessors', function () {
    $config = makeInfileHeadersConfig();

    expect($config->getLlaveFirma())->toBe('VALOR_EN_API_KEY')
        ->and($config->getLlaveApi())->toBe('VALOR_EN_SIGNATURE_KEY');

    $config->setLlaveFirma('FIRMA_NUEVA')->setLlaveApi('API_NUEVA');

    expect($config->getApiKey())->toBe('FIRMA_NUEVA')
        ->and($config->getSignatureKey())->toBe('API_NUEVA');
});

// El constructor nuevo recibe cada llave con el nombre del header al que va,
// sin tocar el constructor histórico (cuyos parámetros están invertidos).
it('builds a config with truthful key names through forInfile', function () {
    $config = FelConfig::forInfile('usuario_demo', 'LLAVE_DE_FIRMA', 'LLAVE_API_REST', [
        'base_url' => 'https://example.test/',
    ]);

    expect($config->getProvider())->toBe('infile')
        ->and($config->getLlaveFirma())->toBe('LLAVE_DE_FIRMA')
        ->and($config->getLlaveApi())->toBe('LLAVE_API_REST');

    $headers = infileHeadersFor($config);

    expect($headers['llaveFirma'])->toBe('LLAVE_DE_FIRMA')
        ->and($headers['llaveApi'])->toBe('LLAVE_API_REST')
        ->and($headers['llave'])->toBe('LLAVE_API_REST');
});

it('includes the identificador header only when an identifier is set', function () {
    $config = makeInfileHeadersConfig();
    expect(infileHeadersFor($config))->not->toHaveKey('identificador');

    $config->setIdentifier('120035502');
    expect(infileHeadersFor($config)['identificador'])->toBe('120035502');
});
