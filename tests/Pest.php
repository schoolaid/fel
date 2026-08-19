<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Determina si hay credenciales reales de INFILE (ambiente de pruebas)
 * cargadas desde el .env para correr los tests de integración en vivo.
 */
function felHasLiveCredentials(): bool
{
    $signature = env('FEL_LLAVE_FIRMA') ?: env('FEL_KEY');
    $api = env('FEL_LLAVE_API') ?: env('FEL_PASSWORD');

    return (bool) (env('FEL_PROVIDER') && env('FEL_USERNAME') && $signature && $api);
}

/**
 * NIT del emisor para los tests de integración. INFILE solo firma documentos
 * cuyo emisor coincide con el NIT de las credenciales. Se toma FEL_ISSUER_NIT
 * si existe; si no, se deriva del usuario de testing (patrón <NIT>_DEMO);
 * como último recurso, el NIT histórico de los tests para corridas sin .env.
 */
function felTestIssuerNit(): string
{
    $nit = env('FEL_ISSUER_NIT');
    if ($nit) {
        return strtoupper((string) $nit);
    }

    $username = (string) env('FEL_USERNAME', '');
    if (preg_match('/^(\d+K?)_/i', $username, $matches)) {
        return strtoupper($matches[1]);
    }

    return '11201169K';
}

/**
 * Guarda un registro de cada documento que los tests de integración crean
 * contra el ambiente de pruebas de INFILE: metadatos en meta.json y los XML
 * (enviado/certificado) como archivos aparte. Un directorio por documento
 * bajo tests/logs/, p. ej. tests/logs/20260818-213000-certify-fact/.
 *
 * @param array<string, mixed> $meta
 * @param array<string, string|null> $xmlParts nombre de archivo => contenido
 */
function felLogDocument(string $name, array $meta, array $xmlParts = []): string
{
    $dir = __DIR__ . '/logs/' . date('Ymd-His') . '-' . $name;
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    file_put_contents(
        $dir . '/meta.json',
        json_encode(
            $meta,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
        ) . "\n"
    );

    foreach ($xmlParts as $filename => $content) {
        if ($content !== null && $content !== '') {
            file_put_contents($dir . '/' . $filename, $content);
        }
    }

    return $dir;
}
