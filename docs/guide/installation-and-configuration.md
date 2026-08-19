# Installation and configuration

## Requirements

- PHP 8.3+
- Laravel 9–12 (`illuminate/support` ^9.0|^10.0|^11.0|^12.0)
- `xmlwriter` and `dom` extensions
- INFILE credentials (see [INFILE credentials](infile-credentials.md))

## Installation

```bash
composer require schoolaid/fel:dev-develop
php artisan vendor:publish --provider="Schoolaid\Fel\FelServiceProvider"
```

> The package does **not** call `mergeConfigFrom`, so publishing the config is
> mandatory if you plan to use `FelConfig::fromConfig()` / environment
> variables. If you build `FelConfig` programmatically (multi-tenant,
> credentials stored in a database), you don't need it.

## Environment variables

| Variable | What it is | Notes |
|---|---|---|
| `FEL_PROVIDER` | Certifier | Only `infile` is supported today |
| `FEL_USERNAME` | INFILE username/prefix | Sent as `UsuarioFirma` and `UsuarioApi` |
| `FEL_LLAVE_FIRMA` | **Signer** key (llave de firma) | Legacy: `FEL_KEY`. See [credentials](infile-credentials.md) |
| `FEL_LLAVE_API` | **REST API** key (llave del API) | Legacy: `FEL_PASSWORD` |
| `FEL_BASE_URL` | Unified-process base URL | `https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml` |
| `FEL_CERTIFY_URL` | Certification endpoint | Relative or absolute, resolved against `base_url` |
| `FEL_CANCEL_URL` | Cancellation (anulación) endpoint | |
| `FEL_STATUS_URL` | Status-check endpoint | Defaults to `consultarEstatus` |
| `FEL_IDENTIFIER` | Static value for the `identificador` header | INFILE treats `identificador` as a **unique per-transaction id** (duplicate control) — prefer `setIdentifier()` per document (e.g. your order id) over a static env value |
| `FEL_TIMEOUT` | HTTP timeout (s) | Default 30 |
| `FEL_VERIFY_SSL` | TLS verification | Default `true`; only disable in development |

> Watch out with `base_url` + relative endpoints: Guzzle resolves the endpoint
> against `base_uri`, so the base URL must end in `/` when the endpoint is
> relative, and an endpoint with a leading `/` replaces the whole path.

## Ways to build a `FelConfig`

**Recommended — the constructor with truthful names:**

```php
use Schoolaid\Fel\Config\FelConfig;

$config = FelConfig::forInfile(
    username: 'my_username',
    llaveFirma: $signerKey,
    llaveApi: $restApiKey,
    providerConfig: [
        'base_url' => '...',
        'certify_url' => '...',
        'cancel_url' => '...',
        'identifier' => '12345678', // NIT (optional)
    ]
);
```

**From the published config / `.env`:**

```php
$config = FelConfig::fromConfig();
```

**From an array** (`FelConfig::fromArray([...])`) and **with setters**
(`setLlaveFirma()` / `setLlaveApi()`, `setIdentifier()`, etc.) — useful for
per-company credentials loaded from a database.

**Historical constructor** (`new FelConfig(provider, username, apiKey,
signatureKey, providerConfig)`): still works for existing apps, but its
parameters have **inverted names** — read
[INFILE credentials](infile-credentials.md) before using it.

## Advanced `provider_config` options

- `client_config` (array): extra options for the Guzzle client. It is merged
  with `timeout`/`verify_ssl` and allows, for example, injecting a test
  `handler` — see [Testing](../testing.md).
