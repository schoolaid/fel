<?php

namespace Tests\Unit;

use Schoolaid\Fel\Config\FelConfig;

it('can create config from constructor', function () {
    $config = new FelConfig(
        'infile',
        'test_user',
        'test_key',
        'test_firma_key',
        [
            'test_option' => 'test_value'
        ]
    );
    $config->setIdentifier('12345678');
    
    expect($config->getProvider())->toBe('infile')
        ->and($config->getUsername())->toBe('test_user')
        ->and($config->getApiKey())->toBe('test_key')
        ->and($config->getSignatureKey())->toBe('test_firma_key')
        ->and($config->getIdentifier())->toBe('12345678');
});

it('can create config from array', function () {
    $config = FelConfig::fromArray([
        'provider' => 'infile',
        'username' => 'test_user',
        'api_key' => 'test_key',
        'llave_firma' => 'test_firma_key',
        'provider_config' => ['test_option' => 'test_value']
    ]);
    
    expect($config->getProvider())->toBe('infile')
        ->and($config->getUsername())->toBe('test_user')
        ->and($config->getApiKey())->toBe('test_key')
        ->and($config->getSignatureKey())->toBe('test_firma_key')
        ->and($config->getProviderConfig())->toBe(['test_option' => 'test_value']);
}); 