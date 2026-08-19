# Hallazgos de la revisión del paquete

Revisión completa del 2026-08-18. Los 8 bugs se confirmaron reproduciéndolos
en ejecución. Ir actualizando la columna de estado conforme se resuelvan.

| # | Hallazgo | Estado |
|---|----------|--------|
| 1 | Inyección / XML malformado por texto sin escapar | ✅ Resuelto |
| 2 | `checkStatus` nunca envía el UUID | ✅ Resuelto |
| 3 | TypeError en `FelCertificationService::cancel()` enmascara el error real | ✅ Resuelto |
| 4 | `new Invoice()` sin tipo de documento lanza TypeError | ✅ Resuelto |
| 5 | `new Cancellation(...)` con fecha de anulación null lanza TypeError | ✅ Resuelto |
| 6 | IVA y GranTotal quedan en 0 si el ítem no trae `total` explícito | ✅ Resuelto |
| 7 | `timeout` y `verify_ssl` de la config nunca llegan al cliente HTTP | ✅ Resuelto |
| 8 | `$responseData['fecha']` sin `?? null` en `certify()` | ✅ Resuelto |

Tema relacionado (cerrado): **llaves INFILE con nombres invertidos** — 📝 se
documenta y NO se corrige; ver sección al final.

---

## Detalle

### 1. Inyección / XML malformado — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Xml/Builder/XmlDocumentBuilder.php` → `writeChildren()`.
- **Qué pasaba:** una heurística (`isXmlString`) escribía el texto **sin
  escapar** si "parecía XML". Una descripción como `Camisa talla <M> &
  accesorios` producía un DTE malformado (INFILE lo rechaza) y permitía
  inyectar elementos arbitrarios desde descripciones, direcciones o adendas.
- **Fix:** los valores con clave nombrada siempre se escriben con
  `xmlwriter_text` (escapados); se eliminó la heurística. El XML
  pre-serializado sigue entrando como string de nivel superior o en arrays
  con claves numéricas.
- **Cobertura:** `tests/Unit/XmlEscapingTest.php`. Verificado además que la
  factura de referencia (`factura-referencia.md`) se genera idéntica
  byte a byte antes y después del fix.

### 2. `checkStatus` nunca envía el UUID — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Certification/Providers/InfileProvider.php` →
  `checkStatus()`. Se construía `$statusUrl` con el UUID pero nunca se usaba;
  `StatusAction::url()` devolvía solo el endpoint, así que el GET salía sin
  el UUID.
- **Fix:** `StatusAction` ganó `setUuid()` y su `url()` incluye
  `?uuid=<urlencode>` cuando está definido; `checkStatus()` lo usa y se
  eliminó la variable muerta.
- **Cobertura:** `tests/Unit/InfileStatusTest.php` — construye la URL con el
  uuid codificado y verifica de punta a punta (vía la URI efectiva del error
  de conexión de Guzzle contra un puerto cerrado) que el GET lleva el uuid.

### 3. TypeError en `cancel()` enmascara el error real — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Certification/FelCertificationService.php` → `cancel()`,
  bloque catch. Llamaba `new CertificationException($e->getMessage(),
  $e->getCode(), $e)` pero el 2.º parámetro es `array $errors` → TypeError
  que ocultaba la excepción original de la anulación.
- **Fix:** el catch usa la firma correcta (mismo estilo que `certify()`):
  mensaje `Cancellation failed: ...`, errores `[]`, y la excepción original
  como `previous`.
- **Cobertura:** `tests/Unit/CancelErrorHandlingTest.php` — fuerza la ruta de
  error real (conexión rechazada) y verifica que llega una
  `CertificationException` con la excepción de Guzzle como `previous`.

### 4. `new Invoice()` sin tipo de documento — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Models/Invoice.php`, constructor. Con `$documentType` null
  asignaba el enum `DocumentTypeEnum::getDefault()` (sin `->value`) a la
  propiedad `string $documentType` → TypeError.
- **Fix:** `DocumentTypeEnum::getDefault()->value`.
- **Cobertura:** `tests/Unit/InvoiceDefaultsTest.php` — `new Invoice()`
  produce `documentType = 'FACT'` y `currencyCode = 'GTQ'`.

### 5. `new Cancellation(...)` con fecha null — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Models/Cancellation.php`, constructor. Con
  `$cancellationDateTime` null asignaba `new \DateTime()` a una propiedad
  `?string` → TypeError. No explotaba vía `FelCancel::fromParams` porque
  siempre pasa string.
- **Fix:** el default se formatea como string (`Y-m-d\TH:i:s`, mismo formato
  que `FelCancel::fromParams`).
- **Cobertura:** `tests/Unit/CancellationDefaultsTest.php`.

### 6. IVA/GranTotal en 0 sin `total` explícito — ✅ Resuelto (2026-08-18)

- **Dónde:** `src/Models/FelItem.php` → `calculateTotal()` era una identidad
  (devolvía `$this->total`), y el IVA se calcula sobre `$item->total`
  (`GeneralTaxCalculator`). Un ítem creado sin `total` producía IVA 0 y
  GranTotal 0 (secuela de los commits "Set total directly in FelItem": el
  fallback original era `total = price`, un cambio intermedio lo volvió
  identidad —lo que crasheaba— y el commit de septiembre asignó directo,
  dejando el 0 silencioso).
- **Fix:** `calculateTotal()` implementa la regla SAT
  (`Total = Precio − Descuento`) y el constructor la usa como fallback cuando
  no se pasa `total`; un `total` explícito se respeta siempre (no se
  sobrescribe: se quitaron las reasignaciones no-op de los calculators y de
  `addTax()`).
- **Cobertura:** `tests/Unit/FelItemTotalTest.php` (default con descuento,
  total explícito preservado, IVA sobre el total calculado) +
  `tests/Unit/Tax/GeneralTaxCalculatorTest.php` en verde. Verificado que la
  factura de referencia sigue byte-idéntica.
- **Extra pendiente (backlog):** nada valida `total = precio − descuento` ni
  `precio = cantidad × precioUnitario` cuando el caller pasa valores
  inconsistentes; SAT sí lo valida.

### 7. `timeout` / `verify_ssl` nunca se aplican — ✅ Resuelto (2026-08-18)

- **Dónde:** `InfileProvider` creaba las acciones con solo
  `(base_url, endpoint)`; el 3.er parámetro `$clientConfig` de
  `BaseFelAction` nunca se pasaba. `FEL_TIMEOUT` y `FEL_VERIFY_SSL` eran
  letra muerta (siempre 30s / true).
- **Fix:** nuevo `InfileProvider::getClientConfig()` traduce
  `timeout`/`verify_ssl` a opciones de Guzzle y se pasa a las 3 acciones
  (certify, cancel, status). Además `provider_config['client_config']`
  permite opciones extra del cliente (p. ej. un handler de pruebas), lo que
  habilita testear el provider con respuestas simuladas sin red.
- **Cobertura:** `tests/Unit/InfileClientConfigTest.php` — mapeo de opciones
  y verificación de que el config llega de verdad al cliente HTTP (vía
  MockHandler).

### 8. `$responseData['fecha']` sin `?? null` — ✅ Resuelto (2026-08-18)

- **Dónde:** `InfileProvider::certify()`, respuesta exitosa. Todos los demás
  campos usaban `?? null`; `fecha` no. Si INFILE no la devolvía: warning de
  índice indefinido (o excepción con handler estricto).
- **Fix:** `$responseData['fecha'] ?? null`.
- **Cobertura:** `tests/Unit/InfileCertifyResponseTest.php` — certificación
  exitosa simulada sin `fecha`, con warnings convertidos en excepción para
  detectar accesos a índices ausentes.

---

## Cerrado: llaves INFILE con nombres invertidos — 📝 Documentado

El mapeo `apiKey → llaveFirma` y `signatureKey → llaveApi` está **invertido
respecto a los nombres**, pero es el contrato vigente: los consumidores
existentes (marketaid y al menos un paquete externo sin acceso) ya pasan las
llaves intercambiadas para compensar. **No se corrige**; el mapeo está fijado
por `tests/Unit/InfileHeadersTest.php` y comentado en `InfileProvider`,
`FelConfig` y `config/fel.php`.

Para código nuevo existen los nombres veraces (todo aditivo, 2026-08-18):

- Constructor `FelConfig::forInfile($username, $llaveFirma, $llaveApi)`.
- Env vars `FEL_LLAVE_FIRMA` / `FEL_LLAVE_API` (legados `FEL_KEY` /
  `FEL_PASSWORD` siguen soportados como respaldo).
- Accesores `getLlaveFirma()` / `setLlaveFirma()` y `getLlaveApi()` /
  `setLlaveApi()`.

---

## Hallazgos nuevos — investigación de notas de crédito (2026-08-18)

Contexto completo en `notas-credito-investigacion.md`. Para revisión:

| # | Hallazgo | Estado |
|---|----------|--------|
| 9 | `DocumentTypeEnum::EXPORT_INVOICE = 'FEXP'` no es un tipo de DTE del catálogo SAT (la exportación es `FACT` con `Exp="SI"` + complemento Exportaciones). El flujo de exportación actual no certificaría. | ⏳ Pendiente de revisión |
| 10 | README sección "Nota de Crédito (NCRE)" (~621-651): el ejemplo usaba `FelAddenda` con namespace de complemento que `AdendaElement` descarta → NCRE sin el complemento `ReferenciasNota` que SAT exige → rechazo garantizado. | ✅ Resuelto (2026-08-18) |

- **#10 / soporte NCRE — resuelto:** se implementó el soporte real de
  notas de crédito/débito: `FelReferenceNote` (Models), `ComplementsElement` +
  `ReferenceNoteXmlTags` (Xml), `CreditNoteGenerator`/`DebitNoteGenerator`
  (rama NCRE/NDEB en `DocumentGeneratorFactory`), propiedad
  `Invoice::setReferenceNote()`, y hijos condicionales en
  `EmissionDataElement` (Complementos después de Totales). Sin referencia,
  `generateXml()` de una NCRE/NDEB lanza `XmlGenerationException`. El README
  fue reescrito con el uso real. Cobertura: `tests/Unit/CreditNoteTest.php`
  (7 tests: complemento completo, excepción sin referencia, NDEB, régimen
  antiguo, serie/número obligatorios, escapado de MotivoAjuste, no-regresión
  en FACT) + `tests/Unit/CreditNoteCertifyTest.php` (integración: FACT origen
  → NCRE certificada de verdad contra el sandbox de INFILE, UUID
  `A1A1134A-…`). Dato del XSD real: `SerieDocumentoOrigen` y
  `NumeroDocumentoOrigen` son obligatorios y se toman de la respuesta de
  certificación del origen (no se derivan del UUID).
- Menores para el backlog: `ReceiverXmlTags::SpecialType` declarado y nunca
  emitido; `FelOrderData` es un modelo huérfano (ningún elemento XML lo lee).

---

## Backlog de hallazgos menores

**Tests**
- La suite completa no corre: `createTestInvoice()` está duplicada en
  `DonationReceiptTest.php` y `FelCancelTest.php` → fatal.
- `TestCase` exige un `.env` físico (`Dotenv::load()`); con `safeLoad()`
  correría en CI/clonado limpio.
- `FELDataStructureValidationTest` termina en un `dd()` (no es un test real);
  `FelCertifyTest` hace HTTP real contra la URL configurada.

**XML / SAT**
- `xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0"` en `GTDocument`
  no coincide con el namespace 0.2.0 y no cumple el formato de pares; mejor
  omitirlo.
- FPEQ: `GeneralTaxCalculator` omite el bloque de IVA; SAT normalmente exige
  `Impuesto IVA` con `CodigoUnidadGravable=2` y `MontoImpuesto=0`. Verificar
  con INFILE antes de emitir FPEQ.
- `FelTax`: `taxAmount = amount − taxableAmount` sin `round()`; las sumas de
  totales tampoco redondean.
- La fecha de emisión por defecto usa la zona horaria del servidor
  (`Invoice.php`); forzar `America/Guatemala` para el `-06:00`.
- `AdendaElement`: dos adendas con el mismo `name` se sobreescriben en
  silencio; la propiedad `namespace` de `FelAddenda` nunca se usa.

**Código muerto / documentación**
- `src/Certification/Providers/ProviderInterface.php`: namespace equivocado
  (no cumple PSR-4) y referencia clases inexistentes. Borrar.
- Sin uso: `Credentials`, `CancellationGenerator::formatDateTime()`.
- README: usa `getSerial()` (el método real es `getSeries()`) y un ejemplo de
  `Cancellation` con argumentos nombrados inexistentes (`uuid:`, `dateTime:`).
- `FelServiceProvider` no hace `mergeConfigFrom` → `config('fel')` es null
  hasta publicar el config.
- `composer.lock` desactualizado respecto a `composer.json`.
- En `FelConfig::fromArray`, la clave alterna `llave_firma` alimenta el slot
  `signature_key` (que viaja como `llaveApi`) — también invertida; no cambiar
  sin revisar consumidores.
