# Operations

The three operations against INFILE: **issue/certify**, **cancel**
(anulación), and **check status**. All of them start from a `FelConfig`
([how to build one](installation-and-configuration.md)).

## Issue and certify

```php
use Schoolaid\Fel\Actions\{FelGenerate, FelCertify};
use Schoolaid\Fel\Enums\{DocumentTypeEnum, CurrencyEnum, IVAAffiliationTypeEnum};
use Schoolaid\Fel\Models\{Invoice, FelIssuer, FelReceiver, FelAddress,
    FelItems, FelItem, FelPhrases, FelPhrase, FelTotals, FelAddenda};

$invoice = new Invoice(
    DocumentTypeEnum::LOCAL_INVOICE,               // FACT
    '2026-07-11T08:09:21-06:00',                   // see the note on dates below
    CurrencyEnum::QUETZAL,
    new FelIssuer('mail@issuer.com', '1', '120035502', 'Trade Name',
        IVAAffiliationTypeEnum::General, 'Legal Name, S.A.',
        new FelAddress('Address...', '01010', 'Municipality', 'Department', 'GT')),
    new FelReceiver('CF', 'mail@customer.com', 'Customer Name',
        new FelAddress('Ciudad', '01010', 'Guatemala', 'Guatemala', 'GT')),
    new FelPhrases([new FelPhrase('2', '1')]),     // (scenario, phrase type)
    new FelItems([
        // (line, B|S, unitPrice, unit, description, price, quantity,
        //  discount, taxes[], total)
        new FelItem(1, 'S', 50, 'UND', '2026 - DAY PASS', 50, 1, 0, [], 50),
    ]),
    new FelTotals(),                               // computed at generation time
    [new FelAddenda('https://www.sat.gob.gt/fel/addenda', 'Orden', 'Order #14068')]
);

// XML only (nothing sent to INFILE):
$xml = (new FelGenerate($invoice))->generateXml();

// Certify:
$response = (new FelCertify($invoice, $config))->execute();

if ($response->isSuccessful()) {
    $response->getUuid();               // SAT authorization
    $response->getSeries();             // series (note: getSeries, not getSerial)
    $response->getNumber();
    $response->getCertificationDate();  // may be null
    $response->getCertifiedXml();
} else {
    $response->getErrors();             // array of INFILE messages
}
```

Taxes and totals are computed automatically at generation time — rules in
[Taxes and totals](taxes-and-totals.md).

### Emission dates

If you omit `emissionDateTime`, `now()` is used **with the server's
timezone**. SAT expects Guatemala time (`-06:00`); on UTC servers pass the
date explicitly with `America/Guatemala`:

```php
now()->setTimezone('America/Guatemala')->format('Y-m-d\TH:i:sP')
```

### Document types

| Enum | Type | Status in the package |
|---|---|---|
| `LOCAL_INVOICE` | FACT | ✅ Production-proven |
| `DONATION_RECEIPT` | RDON | ✅ Dedicated generator, no taxes |
| `SPECIAL_INVOICE` | FESP | Generates with VAT (general generator) |
| `EXCHANGE_INVOICE`, `RECEIPT`, etc. | FCAM, RECI… | Fall through to the general generator; no specific treatment |
| `CREDIT_NOTE`, `DEBIT_NOTE` | NCRE, NDEB | 🔧 In development (`ReferenciasNota` complement) — see [research](../seguimiento/notas-credito-investigacion.md) |
| `EXPORT_INVOICE` | "FEXP" | ⚠️ Not a SAT catalog type (exports = FACT with `Exp="SI"` + complement); pending review |
| `SMALL_TAXPAYER_INVOICE` | FPEQ | ⚠️ Generated without the VAT block; verify with INFILE (SAT usually requires exempt VAT, code 2) |

### Addendas

`FelAddenda(namespace, name, value)` → `<dte:Adenda><Name>value</Name>`.
Caveats: the `namespace` is **not emitted** (it's ignored), and two addendas
with the same `name` silently overwrite each other. Addendas are **not** a
substitute for fiscal complements (e.g. credit-note references) — those
require `dte:Complementos` (in development).

## Cancellation (anulación)

```php
use Schoolaid\Fel\Actions\FelCancel;

$cancel = FelCancel::fromParams(
    uuid: 'DTE-UUID',
    issuerNit: '120035502',
    reason: 'Cancelled due to product return',
    config: $config,
    idReceiver: 'CF',                            // receiver NIT or CF
    documentDateTime: '2026-07-11T08:09:21',     // original DTE date
);

$response = $cancel->execute();   // CancellationResponse
$response->isSuccessful();
$response->getErrors();
```

You can also build `new Cancellation(documentUuid, nitIssuer, idReceiver,
reason, documentDateTime, cancellationDateTime?)` (positional parameters) and
pass it to `new FelCancel($cancellation, $config)`.

> SAT rule: a DTE with an active credit/debit note attached **cannot be
> cancelled** — adjustments must go through an NCRE instead.

## Check status

```php
use Schoolaid\Fel\Certification\FelCertificationService;

$service = FelCertificationService::fromConfig($config);
$status = $service->checkStatus('DTE-UUID');  // StatusResponse

$status->isSuccessful();
$status->getStatus();      // e.g. 'CERTIFICADO'
$status->isCertified();
$status->isCancelled();
```

## Error handling

Two levels:

1. **INFILE rejection** (HTTP response with `resultado: false`): the response
   arrives with `isSuccessful() === false` and the messages in `getErrors()`.
2. **Transport or unexpected failure**: `FelCertificationService` throws a
   `CertificationException`; the original cause (e.g. the Guzzle exception)
   is available via `getPrevious()`.

```php
use Schoolaid\Fel\Certification\Exceptions\CertificationException;

try {
    $response = (new FelCertify($invoice, $config))->execute();
    if (! $response->isSuccessful()) {
        Log::error('FEL rejected', ['errors' => $response->getErrors()]);
    }
} catch (CertificationException $e) {
    Log::error('FEL failed', ['message' => $e->getMessage(), 'cause' => $e->getPrevious()?->getMessage()]);
}
```
