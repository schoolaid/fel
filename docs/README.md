# `schoolaid/fel` documentation

Laravel package for issuing Electronic Tax Documents (DTE) under Guatemala's
FEL regime through the **INFILE** certifier: it generates the XML, certifies
it, cancels documents, and checks their status.

This directory is the source of truth for the documentation. The root
`README.md` is the quick introduction; everything else lives here.

## Map

### Usage guides (`guide/`)

| Document | What it covers |
|---|---|
| [Installation and configuration](guide/installation-and-configuration.md) | Requirements, installation, environment variables, and the ways to build a `FelConfig` |
| [INFILE credentials](guide/infile-credentials.md) | The INFILE keys, which header each one feeds, and the historical name inversion (read it before configuring) |
| [Operations](guide/operations.md) | Issue/certify, cancel, and check status; document types and error handling |
| [Taxes and totals](guide/taxes-and-totals.md) | SAT rules implemented: VAT included in the price, `Total = Price − Discount`, per-type calculators |

### Technical reference

| Document | What it covers |
|---|---|
| [Architecture](architecture.md) | Package layers, XML generation and certification flows, extension points, and the XML serializer rules |
| [Testing](testing.md) | Running the suite, live integration tests against the INFILE sandbox, mocking INFILE without network access, and the reference invoice |

### Tracking documents (`seguimiento/`)

**Living** working documents, kept in Spanish — they are updated as the
package evolves:

| Document | What it is |
|---|---|
| [Review findings](seguimiento/hallazgos-revision.md) | Bug tracking and backlog from the 2026-08-18 review (all 8 main bugs: resolved) |
| [Reference invoice](seguimiento/factura-referencia.md) | A real certified DTE, used as a byte-for-byte fixture to validate XML generation |
| [Research: credit notes](seguimiento/notas-credito-investigacion.md) | SAT rules for NCRE/NDEB and the design of the Complementos support (in development) |
| [Audit: marketaid billing](seguimiento/auditoria-marketaid-billing.md) | Compatibility verdict and prioritized findings for marketaid's INFILE billing layer (handoff document) |

## Conventions

- **`guide/`** — how to use the package from an application. Runnable
  examples, with the warnings placed exactly where they bite.
- **`docs/` root** — reference material for whoever develops the package.
- **`seguimiento/`** — tracking, fixtures, and research. When a finding is
  resolved, its table row and detail section are updated; new research goes
  here as its own file.
- Links between documents are relative; if you move a file, update its
  references.
