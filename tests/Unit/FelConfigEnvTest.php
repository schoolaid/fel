<?php

namespace Tests\Unit;

function felConfigEnvSet(array $vars): void
{
    foreach ($vars as $name => $value) {
        if ($value === null) {
            unset($_ENV[$name], $_SERVER[$name]);
            putenv($name);
        } else {
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

function felConfigFromFile(): array
{
    return require dirname(__DIR__, 2) . '/config/fel.php';
}

const FEL_CONFIG_ENV_KEYS = [
    'FEL_LLAVE_FIRMA',
    'FEL_LLAVE_API',
    'FEL_KEY',
    'FEL_PASSWORD',
    'FEL_API_KEY',
    'FEL_SIGNATURE_KEY',
    'FEL_IDENTIFIER',
];

beforeEach(function () {
    $this->felEnvBackup = [];
    foreach (FEL_CONFIG_ENV_KEYS as $name) {
        $this->felEnvBackup[$name] = $_ENV[$name] ?? null;
    }
    felConfigEnvSet(array_fill_keys(FEL_CONFIG_ENV_KEYS, null));
});

afterEach(function () {
    felConfigEnvSet($this->felEnvBackup);
});

// Los nombres veraces: FEL_LLAVE_FIRMA alimenta el slot api_key (que viaja
// como llaveFirma) y FEL_LLAVE_API el slot signature_key (que viaja como
// llaveApi). Ver la nota de nombres invertidos en config/fel.php.
it('reads credentials from the truthful env names', function () {
    felConfigEnvSet([
        'FEL_LLAVE_FIRMA' => 'llave_firma_nueva',
        'FEL_LLAVE_API' => 'llave_api_nueva',
        'FEL_IDENTIFIER' => '120035502',
    ]);

    $config = felConfigFromFile();

    expect($config['api_key'])->toBe('llave_firma_nueva')
        ->and($config['signature_key'])->toBe('llave_api_nueva')
        ->and($config['provider_config']['identifier'])->toBe('120035502');
});

it('falls back to the legacy FEL_KEY and FEL_PASSWORD names', function () {
    felConfigEnvSet([
        'FEL_KEY' => 'llave_firma_legada',
        'FEL_PASSWORD' => 'llave_api_legada',
    ]);

    $config = felConfigFromFile();

    expect($config['api_key'])->toBe('llave_firma_legada')
        ->and($config['signature_key'])->toBe('llave_api_legada');
});

it('prefers the truthful names over the legacy ones', function () {
    felConfigEnvSet([
        'FEL_LLAVE_FIRMA' => 'gana_la_firma',
        'FEL_KEY' => 'pierde_la_legada',
        'FEL_LLAVE_API' => 'gana_la_api',
        'FEL_PASSWORD' => 'pierde_la_api',
    ]);

    $config = felConfigFromFile();

    expect($config['api_key'])->toBe('gana_la_firma')
        ->and($config['signature_key'])->toBe('gana_la_api');
});
