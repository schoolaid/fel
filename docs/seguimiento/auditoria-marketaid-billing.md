# Auditoría: capa de facturación INFILE de marketaid

Auditoría del 2026-08-18, hecha desde el lado del paquete `schoolaid/fel` tras
los cambios de ese día (`develop`, commits `bca1f91`…`5e1f79f`). Archivos
auditados de marketaid:

- `app/Services/Order/Billing/Providers/Infile/Handlers/CreateBillHandler.php`
- `app/Services/Order/Billing/Providers/Infile/Mappers/InfileInvoiceMapper.php`
- `app/Services/Order/Billing/Providers/Infile/Mappers/DiscountCalculator.php`

**Veredicto de compatibilidad: los cambios del paquete NO rompen a marketaid.**
La auditoría encontró además hallazgos propios de marketaid, priorizados abajo.
Todo lo marcado "verificado" se reprodujo en ejecución contra el paquete real.

---

## 1. Compatibilidad con el paquete (verificada)

| Cambio del paquete | Efecto en marketaid |
|---|---|
| Mapeo de llaves INFILE | Sin cambio: byte-idéntico, fijado por `InfileHeadersTest`. `infile_key`→`apiKey`→`llaveFirma` y `infile_password`→`signatureKey`→`llaveApi` siguen igual. |
| Totales de ítems (fix #6) | Sin cambio: marketaid siempre pasa `total:` explícito y se respeta. Verificado con sus tres patrones, incluido descuento 100 % (`total: 0.0` → salida idéntica). |
| Escape XML (fix #1) | Solo beneficia: nombres de producto o comentarios con `&`/`<...>` antes producían XML malformado; ahora salen escapados. |
| `timeout`/`verify_ssl` (fix #7) | Ahora sí se aplican los valores del `config/fel.php` publicado de marketaid. Sus defaults (30 s / `true`) coinciden con lo que ocurría antes → sin cambio efectivo. Revisar que ningún `.env` de producción tenga valores "de adorno" que ahora tendrían efecto. |
| `Invoice` default (fix #4) | Mejora: `default_document_type` null ya no lanza TypeError (default `FACT`). |
| `cancel()` (fix #3) y `Cancellation` (fix #5) | Mejora para `CancelBillHandler`: errores reales encadenados en vez de TypeError. |
| `fecha` opcional (fix #8) | Sin cambio de comportamiento, pero expone el hallazgo H1 (abajo). |
| NCRE/NDEB (nuevo) | Sin impacto: parámetro nuevo al final del constructor + marketaid usa named args. |
| `setIdentifier($order->id)` | Correcto y coerciona int→string sin problema. Es además el uso CORRECTO del header `identificador` (id único por transacción para control de duplicidad de INFILE) — mejor que un NIT estático. |

---

## 2. Hallazgos en marketaid (priorizados)

### H1 — ALTA: el éxito se decide con `getCertificationDate()` en vez de `isSuccessful()`

`CreateBillHandler::handle()`:

```php
if ($response->getCertificationDate()) { /* guarda Bill */ } else { /* error */ }
```

`certificationDate` es un **dato opcional** (`?string`; el paquete tolera
oficialmente que INFILE certifique sin `fecha`). La señal de éxito es
`isSuccessful()` (HTTP 200 + `resultado: true`).

**Matriz de divergencia:**

| `isSuccessful()` | `fecha` | Marketaid hoy | Correcto |
|---|---|---|---|
| true | sí | Guarda Bill ✓ | ✓ |
| true | **no** | **BillHistory 'error', sin Bill** | Guardar Bill — el DTE SÍ quedó certificado |
| false | no | Error ✓ | ✓ |
| false | sí (anómalo) | Guardaría Bill de un rechazo | Error |

**Escenario de fallo (factura fantasma):** INFILE certifica (UUID asignado,
correo ya enviado al receptor) pero la respuesta viene sin `fecha` → marketaid
registra error visible al usuario, no guarda `Bill` → la orden parece sin
facturar → reintento → o INFILE deduplica por `identificador` (rescate de
chiripa) o se emite un **segundo DTE por la misma orden** (hay que anular el
sobrante). Si la respuesta repite sin `fecha`, el bucle error→reintento no
termina nunca.

**Fix:**

```php
if ($response->isSuccessful()) {
    return new BillActionResponseDto(true, 'Certification successful', $this->saveBill($order, $response));
}
$this->logBillingErrors($order, $response);
return new BillActionResponseDto(false, 'Error in certification', null, $response->getErrors());
```

y en `saveBill()` desacoplar el timestamp: `$bill->created_at =
$response->getCertificationDate() ?? now();` (mejor aún: columna propia
`certified_at` y dejar `created_at` a Eloquent). Opcional: exigir también
`getUuid()` no nulo antes de guardar.

**Detección de casos históricos** (gracias a que `BillHistory` guarda `payload`):

```sql
SELECT order_id, created_at
FROM bill_histories
WHERE status = 'error'
  AND payload LIKE '%"resultado":true%';
```

Segunda señal: buscar en logs el warning PHP `Undefined array key "fecha"`
cerca de facturación (lo emitía el paquete antes del fix #8). Si ambas
búsquedas salen vacías, el fix es preventivo puro.

### H2 — ALTA: descuento capado en `discount` pero no en `total`

`InfileInvoiceMapper::createOrderItems()`:

```php
discount: $this->discountCalculator->adjustDiscountIfNecessary($totalDiscount, $subtotal), // capado a subtotal
total:    floatval($subtotal - $totalDiscount)                                             // SIN capar
```

Si `$totalDiscount > $subtotal`: `Total` negativo e inconsistente con
`Precio − Descuento` (SAT valida esa igualdad) → rechazo. **Fix:** capar una
vez y usar el mismo valor en ambos campos:

```php
$cappedDiscount = $this->discountCalculator->adjustDiscountIfNecessary($totalDiscount, $subtotal);
// ... discount: $cappedDiscount, total: floatval($subtotal - $cappedDiscount)
```

(El caso exacto `totalDiscount == subtotal` — 100 % de descuento — está
verificado y funciona igual antes y después del fix #6 del paquete.)

### H3 — MEDIA: decimales infinitos en descuentos repartidos

`DiscountCalculator::calculateDiscounts()`:
`discountPerItem = totalDiscount / count(details)`.

**Verificado contra el paquete:** Q100 entre 3 ítems emite
`<dte:Descuento>33.333333333333</dte:Descuento>` y
`<dte:Total>216.66666666667</dte:Total>`. Riesgo de rechazo por precisión y
descuadres de centavos. **Fix:** redondear a 2 decimales por ítem y cargar el
residuo al último ítem (para que Σ descuentos == descuento total exacto).

### H4 — MEDIA (bomba latente): offset `-06:00` hardcodeado

`InfileInvoiceMapper::mapToInvoice()`:
`now()->format('Y-m-d\TH:i:s-06:00')` — el `-06:00` es texto literal, no una
conversión. Funciona solo mientras `APP_TIMEZONE` sea Guatemala; si un deploy
queda en UTC, **todos** los DTE salen fechados 6 h en el futuro y SAT los
rechaza en bloque. **Fix (inmune al servidor):**

```php
now()->setTimezone('America/Guatemala')->format('Y-m-d\TH:i:sP')
```

### H5 — BAJA: `(int)$detail->quantity` trunca cantidades fraccionarias

El subtotal se calcula con la cantidad original
(`sale_price * $detail->quantity`) pero al XML va la truncada. Con
`quantity = 1.5`: `Cantidad=1`, `Precio=1.5×precio` → viola
`Precio = Cantidad × PrecioUnitario` → rechazo garantizado. Si las cantidades
son siempre enteras nunca dispara; el cast no aporta nada
(`FelItem::$quantity` es `float`). **Fix:** `quantity: (float) $detail->quantity`.

### H6 — INFO: el `$total` que calcula el mapper se descarta

Toda la aritmética de `mapToInvoice()` (`order->total` + vencidos por
transactions `expired-discount%` + moras + vencidos por `appliedDiscounts`)
termina en `FelTotals(grandTotal: $total)`… que el paquete **sobrescribe**:
`GranTotal` siempre es Σ totales de ítems (verificado: pasar 999999 con ítems
que suman 150 emite `GranTotal=150` sin aviso). Implicaciones:

- Lo protector: los posibles dobles conteos de esa aritmética (vencidos por
  dos canales; moras que quizá ya estén en `order->total`) **nunca llegan al
  DTE**.
- Lo peligroso: si `$total ≠ Σ ítems`, nadie se entera — la vista contable y
  el documento fiscal divergen en silencio. Lo que no esté como ítem no se
  factura.

**Recomendación:** no borrar la aritmética — convertirla en aserción de
conciliación antes de certificar:

```php
$itemsTotal = round(array_sum(array_map(fn ($i) => $i->total, $allItems)), 2);
if (abs($itemsTotal - round($total, 2)) > 0.01) {
    Log::error("Descuadre de facturación en orden {$order->id}", [
        'total_contable' => $total, 'total_items' => $itemsTotal,
    ]);
    // abortar certificación o registrar en BillHistory, según política
}
```

### H7 — Menores

- `Log::warning(json_encode($response))` serializa `"{}"` (objeto sin
  propiedades públicas) — log inútil en incidentes. Usar contexto:
  `Log::warning("Error al facturar orden {$order->id}", ['tenant' => tenant('id'), 'errors' => $response->getErrors(), 'raw' => $response->getRawResponse()])`.
- `Log::warning(tenant('id'))` como línea suelta es ruido sin contexto.
- Import `AuthenticationException` nunca se lanza (el paquete no la usa).
- PhpStorm: escapes redundantes `\]` en la regex de `extractErrorMessage`
  (trivial).
- Multi-tenant: la dedupe de INFILE por `identificador` es por credenciales.
  Si dos tenants compartieran credenciales, ids de orden podrían colisionar.
  Confirmar que las credenciales son siempre por emisor/tenant.

---

## 3. Lo que está bien (conservar)

- `translateErrorToUserMessage()` es un catálogo excelente de errores reales
  de INFILE traducidos a mensajes de usuario.
- `extractRawErrors()` lee `descripcion_errores[].mensaje_error` — más
  profundo que el `getErrors()` del paquete; correcto conservarlo.
- Named arguments en todo el mapper — blinda contra cambios de firma.
- `setIdentifier($order->id)` — uso correcto del control de duplicidad.

## 4. Datos del paquete útiles para quien haga los fixes

- `provider_config['client_config'] => ['handler' => HandlerStack::create($mock)]`
  permite testear los handlers **sin red** con respuestas simuladas de INFILE
  (ver `docs/testing.md` del paquete). Ideal para el test de regresión de H1.
- El paquete recalcula impuestos y totales al generar: `taxes[]` que pase el
  caller se descarta, `GranTotal` = Σ ítems siempre.
- Referencia completa del paquete: `docs/` de `schoolaid/fel@develop`.

## 5. Orden de ataque sugerido

1. H1 (gating `isSuccessful`) + query de detección de fantasmas históricos.
2. H2 (cap consistente del descuento).
3. H4 (timezone — una línea, elimina la bomba de entorno).
4. H3 (redondeo del reparto de descuentos).
5. H6 (aserción de conciliación), H5 y H7 al pasar.
