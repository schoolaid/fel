# INFILE credentials

INFILE hands you three credentials:

| Credential | What it's for | Header it travels in |
|---|---|---|
| **Username** (prefix) | Authentication | `Usuario`, `UsuarioFirma`, `UsuarioApi` |
| **Signing key** (llave de firma) | Signing documents (electronic signer) | `llaveFirma` |
| **API key** (llave del API) | Authenticating against the REST API | `llaveApi` and `llave` |

Additionally, the `identificador` header is sent when configured
(`FEL_IDENTIFIER` / `provider_config['identifier']` / `setIdentifier()`).
INFILE documents it as a **unique per-transaction identifier** used for
duplicate control — prefer setting it per document
(e.g. `setIdentifier($orderId)`) rather than using a static value.

## ⚠️ The historical name inversion

Inside `FelConfig`, the internal names `apiKey` / `signatureKey` are
**inverted** with respect to what actually gets sent:

| Internal `FelConfig` slot | Actually contains | Header |
|---|---|---|
| `apiKey` (`api_key`) | the **signing key** | `llaveFirma` |
| `signatureKey` (`signature_key`) | the **API key** | `llaveApi`, `llave` |

This is **deliberately not fixed**: existing consumers of the package (some
outside our control) already pass the keys swapped to compensate. Changing the
mapping would silently break their authentication. The contract is pinned by
`tests/Unit/InfileHeadersTest.php` — if that test fails because someone
"corrected" the swap, revert the code, don't adjust the test.

## How to never think about the inversion

Use the **truthful names** exclusively — they place each key where its name
says:

```php
// Constructor
$config = FelConfig::forInfile(
    username: 'my_username',
    llaveFirma: $signerKey,
    llaveApi: $restApiKey,
);

// Setters
$config->setLlaveFirma($signerKey)->setLlaveApi($restApiKey);

// Reads
$config->getLlaveFirma(); // signing key
$config->getLlaveApi();   // REST API key
```

```env
# Environment variables
FEL_LLAVE_FIRMA=...   # signing key
FEL_LLAVE_API=...     # REST API key
```

## If you use the legacy names

| Via | Where the signing key goes | Where the API key goes |
|---|---|---|
| Historical constructor | `apiKey:` | `signatureKey:` |
| `fromArray()` | `'api_key'` | `'signature_key'` |
| Legacy env vars | `FEL_KEY` | `FEL_PASSWORD` |

(Yes: the opposite of what the names suggest. That's why the truthful names
exist.)

> Extra note: in `fromArray()` the alternate key `'llave_firma'` also feeds
> the `signature_key` slot (i.e. it travels as `llaveApi`) — equally inverted,
> kept for compatibility. Avoid it.

## Symptoms of swapped keys

INFILE authentication errors while certifying with "correct" credentials.
Check against the first table: the signing key must end up in the
`llaveFirma` header. With the truthful names this cannot happen.
