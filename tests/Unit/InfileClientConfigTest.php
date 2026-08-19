<?php

namespace Tests\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ReflectionMethod;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Config\FelConfig;

it('maps timeout and verify_ssl into the HTTP client config', function () {
    $config = new FelConfig('infile', 'usuario_demo', 'LLAVE_A', 'LLAVE_B', [
        'base_url' => 'https://example.test/',
        'timeout' => '15',
        'verify_ssl' => false,
    ]);

    $provider = new InfileProvider($config);
    $method = new ReflectionMethod($provider, 'getClientConfig');

    expect($method->invoke($provider))->toBe(['timeout' => 15.0, 'verify' => false]);
});

it('forwards the client config to the HTTP client', function () {
    $mock = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'resultado' => true,
            'status' => 'CERTIFICADO',
        ])),
    ]);

    // Si el provider no reenviara client_config, la petición iría de verdad
    // al puerto cerrado y la consulta fallaría.
    $config = new FelConfig('infile', 'usuario_demo', 'LLAVE_A', 'LLAVE_B', [
        'base_url' => 'http://127.0.0.1:1/',
        'status_url' => 'status',
        'client_config' => ['handler' => HandlerStack::create($mock)],
    ]);

    $response = (new InfileProvider($config))->checkStatus('UUID-1');

    expect($response->isSuccessful())->toBeTrue()
        ->and($response->getStatus())->toBe('CERTIFICADO')
        ->and((string) $mock->getLastRequest()->getUri())->toContain('uuid=UUID-1');
});
