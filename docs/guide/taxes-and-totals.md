# Taxes and totals

How the package computes the amounts of a DTE and which SAT rules it
implements.

## Pricing model: VAT included

The prices you pass in `FelItem` **already include VAT** (as goods are priced
to the public in Guatemala). At generation time the package breaks them down:

```
MontoGravable (taxable) = round(Total / 1.12, 2)
MontoImpuesto (tax)     = Total − MontoGravable
```

Real example (reference invoice): a Q50.00 item → taxable 44.64 +
VAT 5.36 = 50.00.

## Item rules

| Field | Rule |
|---|---|
| `price` | Must equal `quantity × unitPrice`. **The package does not validate it** — that's the caller's responsibility (SAT does validate it). |
| `total` | `price − discount`. If you omit it, the package computes it with that rule; if you pass it, **it is respected as-is** (not validated against price/discount either). |
| VAT | Computed over `total` (VAT-inclusive). |

```php
// total omitted → computed: 100 − 10 = 90, VAT over 90
new FelItem(1, 'S', 100.0, 'UND', 'Service', 100.0, 1, 10.0);

// explicit total → respected
new FelItem(1, 'S', 100.0, 'UND', 'Service', 100.0, 1, 0.0, [], 100.0);
```

## Calculators per document type

At generation time, `TaxCalculatorFactory` picks the calculator, which
**replaces** the item's taxes (whatever you pass in `taxes[]` is discarded):

| Type | Calculator | Taxes |
|---|---|---|
| FACT, FESP, NCRE, NDEB, and anything not listed | `GeneralTaxCalculator` | 12 % VAT per item |
| FEXP, FPEQ | `GeneralTaxCalculator` (no-tax branch) | None |
| RDON | `DonationTaxCalculator` | None |
| (export generator) | `ExportTaxCalculator` | None |

⚠️ **FPEQ:** currently emitted without a VAT block; SAT usually requires for
FPEQ the `Impuesto IVA` with `CodigoUnidadGravable=2` (exempt) and
`MontoImpuesto=0`. Verify with INFILE before issuing — tracked in the
[backlog](../seguimiento/hallazgos-revision.md).

## Document totals

- `GranTotal = Σ total` across the items (computed by the generator; any
  `FelTotals` value you pass is overwritten).
- `TotalImpuestos/TotalImpuesto` groups by tax short name (IVA) and sums
  `MontoImpuesto`. Only emitted when there are taxes.

## Tax catalog (`TaxEnum`)

| Enum | NombreCorto | Rate used | CodigoUnidadGravable |
|---|---|---|---|
| `IVA` | IVA | 12 % | 1 |
| `ISO` | ISO | 1 % | 2 |
| `RETIVA` | RETENCION IVA | 1 % | 3 |
| `ISR` | ISR | 1 % | 4 |

> In practice only IVA is used by the automatic calculation. The taxable-unit
> codes are fixed per tax — a simplification: in the SAT standard the code
> depends on the scenario (e.g. exempt VAT = 2).

## Rounding

`MontoGravable` is rounded to 2 decimals; `MontoImpuesto` and the total sums
are derived through float subtraction/addition **without an explicit
`round()`**. String conversion masks it in practice, but it's a fragile spot
tracked in the [backlog](../seguimiento/hallazgos-revision.md) — if SAT
rejects a document over a one-cent mismatch, start there.
