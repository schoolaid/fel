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
 * Además va acumulando una línea por documento en tests/logs/documentos.md:
 * el libro de todo lo emitido contra el sandbox, corrida tras corrida.
 *
 * @param array<string, mixed> $meta
 * @param array<string, string|null> $xmlParts nombre de archivo => contenido
 */
function felLogDocument(string $name, array $meta, array $xmlParts = []): string
{
    $dir = __DIR__ . '/logs/' . \Schoolaid\Fel\Support\FelDateTime::now('Ymd-His') . '-' . $name;
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

    felAppendDocumentLedger($name, $meta, $dir);

    return $dir;
}

/**
 * Añade el documento al libro acumulativo tests/logs/documentos.md.
 *
 * @param array<string, mixed> $meta
 */
function felAppendDocumentLedger(string $name, array $meta, string $dir): void
{
    $ledger = __DIR__ . '/logs/documentos.md';

    if (! file_exists($ledger)) {
        file_put_contents($ledger, implode("\n", [
            '# Documentos emitidos contra el sandbox de INFILE',
            '',
            'Una línea por documento (certificado o rechazado). Lo escribe',
            '`felLogDocument()` en cada corrida de los tests de integración.',
            '',
            '| Fecha | Escenario | Tipo | Resultado | UUID | Serie | Número | Detalle |',
            '|---|---|---|---|---|---|---|---|',
            '',
        ]));
    }

    $detail = $meta['exception'] ?? null;
    if (! $detail) {
        $errors = (array) ($meta['errors'] ?? []);
        $detail = $errors ? implode(' · ', array_map(
            fn ($e) => is_string($e) ? $e : json_encode($e, JSON_UNESCAPED_UNICODE),
            $errors
        )) : '';
    }
    $detail = str_replace(['|', "\n"], ['/', ' '], (string) $detail);
    if (mb_strlen($detail) > 200) {
        $detail = mb_substr($detail, 0, 197) . '...';
    }

    file_put_contents($ledger, sprintf(
        "| %s | %s | %s | %s | %s | %s | %s | %s |\n",
        \Schoolaid\Fel\Support\FelDateTime::now('Y-m-d H:i:s'),
        $name,
        $meta['documentType'] ?? '',
        ($meta['successful'] ?? false) ? 'certificado' : 'rechazado',
        $meta['uuid'] ?? '—',
        $meta['series'] ?? '—',
        $meta['number'] ?? '—',
        $detail !== '' ? $detail : basename($dir)
    ), FILE_APPEND);
}

/**
 * Datos del emisor para los tests de integración. Equivalen a una fila de
 * `bill_issuers` en la app consumidora (nit, name, commercial_name, email,
 * office_code, iva_affiliation, address/postal_code/city/state/country,
 * phrase_type, phrase_stage, person_type, default_document_type) y se leen
 * del .env para no hornear datos de un contribuyente real en los tests.
 *
 * @return array<string, string|null>
 */
function felTestIssuerData(): array
{
    return [
        'nit' => felTestIssuerNit(),
        'name' => (string) env('FEL_ISSUER_NAME', 'Laid Demo'),
        'commercialName' => (string) env('FEL_ISSUER_COMMERCIAL_NAME', 'Demo'),
        'email' => (string) env('FEL_ISSUER_EMAIL', 'esevitra@gmail.com'),
        'officeCode' => (string) env('FEL_ISSUER_OFFICE_CODE', '1'),
        'ivaAffiliation' => (string) env('FEL_ISSUER_IVA_AFFILIATION', 'GEN'),
        'address' => (string) env('FEL_ISSUER_ADDRESS', 'CIUDAD DE GUATEMALA'),
        'postalCode' => (string) env('FEL_ISSUER_POSTAL_CODE', '01001'),
        'city' => (string) env('FEL_ISSUER_CITY', 'Guatemala'),
        'state' => (string) env('FEL_ISSUER_STATE', 'Guatemala'),
        'country' => (string) env('FEL_ISSUER_COUNTRY', 'GT'),
        'phraseType' => (string) env('FEL_ISSUER_PHRASE_TYPE', '1'),
        'phraseStage' => (string) env('FEL_ISSUER_PHRASE_STAGE', '1'),
        'personType' => env('FEL_ISSUER_PERSON_TYPE') ?: null,
        'defaultDocumentType' => (string) env('FEL_ISSUER_DEFAULT_DOCUMENT_TYPE', 'FACT'),
    ];
}

/**
 * Emisor de pruebas construido a partir de felTestIssuerData().
 */
function felTestIssuer(): \Schoolaid\Fel\Models\FelIssuer
{
    $data = felTestIssuerData();

    return new \Schoolaid\Fel\Models\FelIssuer(
        $data['email'],
        $data['officeCode'],
        $data['nit'],
        $data['commercialName'],
        \Schoolaid\Fel\Enums\IVAAffiliationTypeEnum::from($data['ivaAffiliation']),
        $data['name'],
        new \Schoolaid\Fel\Models\FelAddress(
            $data['address'],
            $data['postalCode'],
            $data['city'],
            $data['state'],
            $data['country']
        )
    );
}

/**
 * Frase por defecto del emisor (bill_issuers.phrase_type / phrase_stage).
 * FelPhrase recibe (CodigoEscenario, TipoFrase): el "stage" es el escenario.
 */
function felTestIssuerPhrases(): \Schoolaid\Fel\Models\FelPhrases
{
    $data = felTestIssuerData();

    return new \Schoolaid\Fel\Models\FelPhrases([
        new \Schoolaid\Fel\Models\FelPhrase($data['phraseStage'], $data['phraseType']),
    ]);
}

/**
 * Fecha de emisión en hora de Guatemala, con el mismo reloj que usa el
 * paquete cuando no se pasa fecha. Testbench corre en UTC, así que un test
 * que construya la fecha con now() adelantaría el DTE 6 horas.
 */
function felEmissionDateTime(): string
{
    return \Schoolaid\Fel\Support\FelDateTime::now('Y-m-d\TH:i:s');
}
