# Testing

Test suite built on [Pest](https://pestphp.com/) over Orchestra Testbench.

## Running the suite

```bash
composer install
./vendor/bin/pest                      # everything
./vendor/bin/pest tests/Unit/XmlGenerationTest.php   # a single file
```

No `.env` is needed for the unit tests (`TestCase` uses `safeLoad()`).

## Live integration tests (optional)

With a `.env` based on `.env.example` and **INFILE sandbox** credentials, the
integration tests detect the credentials via `felHasLiveCredentials()` (in
`tests/Pest.php`) and run against the real service. Without credentials, they
are skipped.

```bash
./vendor/bin/pest --group=integration            # live tests only
./vendor/bin/pest --exclude-group=integration    # unit tests only
```

- **Issuer NIT**: INFILE only signs XML whose issuer matches the NIT of the
  credentials' signing certificate (which is not always the `<NIT>_DEMO`
  username prefix — INFILE reports the real NIT in `descripcion_errores` when
  it doesn't match). Configure it with `FEL_ISSUER_NIT` in the `.env`; the
  tests read it via `felTestIssuerNit()`.
- **Per-document logs**: every live certification/cancellation leaves a directory in
  `tests/logs/` (git-ignored) with `meta.json` (uuid, series, number, errors,
  and the full raw response), `request.xml`, and `certified.xml`, via
  `felLogDocument()`. They serve as evidence and to diagnose INFILE/SAT
  rejections.
- **RDON (donation receipt)**: the live test is permanently skipped — SAT
  validates against its registry and the demo NIT (GEN regime) cannot issue
  RDON (rules 2541/2543/25301/25401). Re-enabling it requires credentials
  from an authorized donee entity.

> The `.env` with credentials is never committed (it's in `.gitignore`).

## Mocking INFILE without network access

`provider_config['client_config']` accepts Guzzle client options, which
allows injecting a mock handler and testing the full provider flow without
touching the network:

```php
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Schoolaid\Fel\Config\FelConfig;

$mock = new MockHandler([
    new Response(200, ['Content-Type' => 'application/json'], json_encode([
        'resultado' => true,
        'uuid' => 'TEST-UUID',
        'serie' => 'AAA',
        'numero' => '123',
    ])),
]);

$config = FelConfig::forInfile('username', 'signer_key', 'api_key', [
    'base_url' => 'http://127.0.0.1:1/',   // closed port: if the mock doesn't apply, it fails instantly
    'certify_url' => 'certify',
    'client_config' => ['handler' => HandlerStack::create($mock)],
]);
```

`$mock->getLastRequest()` additionally lets you inspect the sent request
(URI, headers, body). Real examples: `tests/Unit/InfileClientConfigTest.php`
and `tests/Unit/InfileCertifyResponseTest.php`.

Another useful pattern (no mock): pointing `base_url` at
`http://127.0.0.1:1/` forces the connection-error path instantly and without
network — Guzzle includes the effective URI in the message, which lets you
assert which URL was attempted (`tests/Unit/InfileStatusTest.php`).

## Reference invoice

[`seguimiento/factura-referencia.md`](seguimiento/factura-referencia.md)
contains a real certified DTE. Changes to the XML layer must verify that this
invoice keeps generating **byte-for-byte identical** (that's how the escaping
and totals fixes were validated). Pattern: generate with the same input data
and compare with `diff`.

## Conventions

- **TDD**: every fix or feature lands with its test seen failing first (red →
  green). Bug-regression tests reference the finding they cover
  ([seguimiento/hallazgos-revision.md](seguimiento/hallazgos-revision.md)).
- Tests that pin **intentional contracts** (e.g. the inverted key mapping in
  `InfileHeadersTest`) carry a comment explaining why they must not be
  "fixed".
- Helpers inside a test file get unique names (the `Tests\Unit` namespace is
  shared and duplicate functions break the whole suite).
