<?php

namespace Tests\Unit;

use Schoolaid\Fel\Certification\Actions\StatusAction;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Config\FelConfig;

it('builds the status URL with the url-encoded uuid', function () {
    $action = new StatusAction('https://example.test/', 'status');

    expect($action->url())->toBe('status');

    $action->setUuid('ABC 123+x');

    expect($action->url())->toBe('status?uuid=ABC+123%2Bx');
});

it('sends the uuid in the status request URI', function () {
    // Puerto 1 en loopback: la conexión se rechaza al instante y sin red.
    // Guzzle incluye la URI efectiva en el mensaje del error de conexión,
    // lo que permite verificar sin servidor que el GET lleva el uuid.
    $config = new FelConfig(
        'infile',
        'usuario_demo',
        'LLAVE_A',
        'LLAVE_B',
        [
            'base_url' => 'http://127.0.0.1:1/',
            'status_url' => 'status',
        ]
    );

    $response = (new InfileProvider($config))->checkStatus('11AA22BB-C3D4-E5F6-A7B8-90CD12EF34AB');

    expect($response->isSuccessful())->toBeFalse()
        ->and($response->getErrors()[0])->toContain('uuid=11AA22BB-C3D4-E5F6-A7B8-90CD12EF34AB');
});
