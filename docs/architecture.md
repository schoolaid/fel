# Architecture

## Overview

Two independent pipelines that meet at the high-level actions:

```
ISSUANCE (local, no network)
Invoice (Models/Fel*)
  └─ FelGenerate
       └─ DocumentGeneratorFactory ──► Generator (General | DonationReceipt | Export | …)
            ├─ TaxCalculatorFactory ──► TaxCalculator (taxes + totals)
            └─ Elements (GTDocument ► SAT ► DTE ► DatosEmision ► …)
                 └─ XmlDocumentBuilder (ext-xmlwriter) ──► GTDocumento XML

CERTIFICATION / CANCELLATION / STATUS (HTTP)
FelCertify | FelCancel | checkStatus
  └─ FelCertificationService (wraps failures in CertificationException)
       └─ InfileProvider (credential headers + response parsing)
            └─ CertifyAction | CancelAction | StatusAction (BaseFelAction + Guzzle)
                 ──► Responses (CertificationResponse | CancellationResponse | StatusResponse)
```

Cancellation generates its XML separately: `CancellationGenerator` (DOMDocument)
produces `dte:GTAnulacionDocumento` (namespace 0.1.0), distinct from the
issuance document (`dte:GTDocumento`, namespace 0.2.0, `Version="0.1"`).

## Directory map

| Path | Role |
|---|---|
| `src/Actions/` | High-level API: `FelGenerate`, `FelCertify`, `FelCancel` |
| `src/Models/` | Domain models (`Invoice`, `FelItem`, `FelTax`, `Cancellation`, …) |
| `src/Enums/` | Document types, currencies, taxes, VAT affiliation |
| `src/Xml/Generators/` | One generator per document family; `AbstractInvoiceGenerator` orchestrates taxes + elements |
| `src/Xml/Elements/` | One object per DTE node (`XmlSerializable::asXML()`) |
| `src/Xml/Enums/` | XML tag/attribute names |
| `src/Xml/Builder/` | `XmlDocumentBuilder`: the only gateway to `xmlwriter` |
| `src/Services/Tax/` | Per-type tax calculators |
| `src/Certification/` | Everything HTTP: INFILE provider, actions, responses, exceptions |
| `src/Config/` | `FelConfig` (credentials + provider_config) |
| `src/Support/` | `FelDateTime`: the `America/Guatemala` clock every default date uses |

## XML serializer rules

`XmlDocumentBuilder::buildElement(name, attributes, children)` accepts as
children:

- **Top-level string** → written **raw** (pre-serialized XML from another
  element).
- **Array with numeric keys** → each string is written **raw** (a list of
  pre-serialized elements).
- **Array with named keys** → `<key>value</key>` with the value **always
  escaped** (`xmlwriter_text`).

Safety rule: user data (descriptions, addresses, addenda values) must enter
**only** through named keys. Never reintroduce "looks like XML → write raw"
heuristics: that was injection bug #1 (see
[findings](seguimiento/hallazgos-revision.md)). Coverage:
`tests/Unit/XmlEscapingTest.php`.

Attributes and elements whose value is `null` or `''` are **omitted** — watch
out for fields SAT requires even when empty.

## Certification layer (INFILE)

- `InfileProvider::getCommonHeaders()` assembles the credentials. **The key
  mapping is deliberately inverted relative to the internal names** — read
  [INFILE credentials](guide/infile-credentials.md) before touching it; the
  contract is pinned by `tests/Unit/InfileHeadersTest.php`.
- `getClientConfig()` translates `timeout`/`verify_ssl` into Guzzle options
  and merges `provider_config['client_config']` (the injection point for
  tests).
- INFILE responses: JSON with `resultado` (bool), `uuid`, `serie`, `numero`,
  `fecha`, `xml_certificado`, and `descripcion`/`mensaje` on errors. The
  provider tolerates missing fields and non-JSON `Content-Type`
  (`raw_content`).

## Extension points

| I want to… | Touch |
|---|---|
| Add a document type with different XML | New generator extending `AbstractInvoiceGenerator` + a branch in `DocumentGeneratorFactory` |
| Add a tax policy | New calculator (`TaxCalculatorInterface`) + a branch in `TaxCalculatorFactory` |
| Add another certifier | Implement `Certification\Contracts\ProviderInterface` + a branch in `FelCertificationService::createDefaultProvider()` (only `infile` today) |
| Add a new DTE node | New Element + hook it into `EmissionDataElement` (children are in fixed order — respect the XSD order) |

## Credit and debit notes (NCRE/NDEB)

`dte:Complementos` > `cno:ReferenciasNota`, built from the `FelReferenceNote`
model by `ComplementsElement` and emitted after `dte:Totales`. The complement
is **required** for NCRE/NDEB and **forbidden** everywhere else
(`ComplementsElement::appliesTo()`), so `AbstractInvoiceGenerator` refuses both
mistakes before the request leaves the process. Verified live against the
INFILE sandbox — SAT rules and scenario results in
[the research doc](seguimiento/notas-credito-investigacion.md).

## Known limitations

The full state (resolved bugs, XML/SAT backlog, dead code) lives in
[seguimiento/hallazgos-revision.md](seguimiento/hallazgos-revision.md).
Highlights: incorrect `schemaLocation` in `GTDocument`, FPEQ without a VAT
block, missing `round()` calls.
