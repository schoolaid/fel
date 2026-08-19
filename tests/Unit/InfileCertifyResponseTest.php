<?php

namespace Tests\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Config\FelConfig;

it('certifies successfully even when the response has no fecha', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'resultado' => true,
            'uuid' => 'UUID-CERT',
            'serie' => 'AAA',
            'numero' => '123',
            'xml_certificado' => '<xml/>',
        ])),
    ]);

    $config = new FelConfig('infile', 'usuario_demo', 'LLAVE_A', 'LLAVE_B', [
        'base_url' => 'http://127.0.0.1:1/',
        'certify_url' => 'certify',
        'client_config' => ['handler' => HandlerStack::create($mock)],
    ]);

    // Warnings (p. ej. "Undefined array key") se convierten en excepción para
    // que el test falle si el provider accede a campos ausentes sin ?? null.
    set_error_handler(function (int $errno, string $errstr): bool {
        throw new \ErrorException($errstr);
    });

    try {
        $response = (new InfileProvider($config))->certify('<dte/>');
    } finally {
        restore_error_handler();
    }

    expect($response->isSuccessful())->toBeTrue()
        ->and($response->getUuid())->toBe('UUID-CERT')
        ->and($response->getCertificationDate())->toBeNull();
});
