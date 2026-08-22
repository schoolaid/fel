# Notas de crédito (NCRE) — investigación SAT + estado del paquete

Investigación del 2026-08-18. Fuentes: SAT «Reglas y Validaciones FEL» v1.7.6
(julio 2023, portal SAT), ejemplos XML oficiales de certificadores (Megaprint),
y revisión completa del código del paquete. Certificador objetivo: INFILE.

> **Actualización (2026-08-19):** revisión en vivo con la matriz de
> escenarios `tests/Unit/CreditNoteScenariosTest.php` (FACT origen, NCRE
> parcial, segunda NCRE por el saldo, NDEB y tres controles negativos). Salió
> con dos bugs corregidos —el complemento se emitía en cualquier tipo de
> documento, y `getErrors()` descartaba el detalle de la SAT— y con tres
> comportamientos confirmados: **se aceptan varias notas sobre la misma
> factura**, la **NDEB** funciona igual que la NCRE, y la SAT rechaza por
> UUID origen inexistente (3.5.1 No. 1) y por receptor distinto al del origen
> (3.5.1 No. 5). Detalle en `hallazgos-revision.md` (#11 y #12).
>
> **Actualización (2026-08-18):** el soporte descrito en la sección 4 ya fue
> implementado (`FelReferenceNote`, `ComplementsElement`, `CreditNoteGenerator`
> / `DebitNoteGenerator`, `Invoice::setReferenceNote()`; cobertura en
> `tests/Unit/CreditNoteTest.php`) y el README fue corregido. **Verificado de
> punta a punta contra el sandbox de INFILE**
> (`tests/Unit/CreditNoteCertifyTest.php`, grupo `integration`): se certifica
> una FACT origen y luego la NCRE que la referencia; primera NCRE certificada:
> UUID `A1A1134A-C6F5-4D7D-940A-ABC025C7D5F6` (origen `21C525CE-…`, logs en
> `tests/logs/`). Lo que sigue se conserva como referencia normativa y del
> estado previo.

**Conclusión corta (estado previo a la implementación):** el paquete no podía
emitir una NCRE válida. El enum `NCRE` existía y toda la capa HTTP de INFILE es
agnóstica al tipo, pero no había soporte para el nodo `dte:Complementos`, que
es **obligatorio** en NCRE. El ejemplo del README (líneas ~621-651) que
"resolvía" la referencia vía adendas era incorrecto: el serializador descarta
el namespace y la SAT rechazaría el DTE por complemento requerido ausente.

---

## 1. Qué exige la SAT para una NCRE

### 1.1 Naturaleza y base legal

- La NCRE ajusta (devolución, descuento, corrección) parcial o totalmente una
  factura ya certificada. No es un documento independiente: referencia el UUID
  del DTE origen y la SAT los enlaza.
- Anulación vs NCRE: la anulación aplica cuando la operación no se concretó
  (además, un DTE que ya tiene una NCRE/NDEB vigente asociada **no puede
  anularse** — validación 3.13 del doc de reglas). Para ajustes posteriores,
  corresponde NCRE.
- **Plazo:** la Ley del IVA da 2 meses desde la emisión de la factura afectada.
  Pasado el plazo la SAT **no rechaza** la NCRE, pero el receptor pierde el
  derecho al crédito fiscal (art. 17 Ley del IVA; aclaración 2, sección 2.7 del
  doc de reglas).

### 1.2 Estructura XML

Una NCRE es un DTE normal (mismo `dte:GTDocumento` 0.2.0, DatosGenerales,
Emisor, Receptor, Frases, Items, Totales) con dos diferencias:

1. `dte:DatosGenerales Tipo="NCRE"`.
2. Nodo `dte:Complementos` **después de `dte:Totales`**, dentro de
   `dte:DatosEmision`, con el complemento ReferenciasNota:

```xml
<dte:Complementos>
    <dte:Complemento IDComplemento="1"
                     NombreComplemento="NOTA CREDITO"
                     URIComplemento="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0">
        <cno:ReferenciasNota Version="1"
            xmlns:cno="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0"
            NumeroAutorizacionDocumentoOrigen="40A3AC05-4143-4468-B833-FA4D216AC731"
            FechaEmisionDocumentoOrigen="2020-02-26"
            MotivoAjuste="DESCUENTO"/>
    </dte:Complemento>
</dte:Complementos>
```

(Ejemplo real de Megaprint. `IDComplemento`, `NombreComplemento` y
`URIComplemento` son atributos requeridos del nodo `Complemento`.)

Atributos de `cno:ReferenciasNota`:

| Atributo | Uso |
|---|---|
| `NumeroAutorizacionDocumentoOrigen` | UUID del DTE origen (régimen FEL) o número de resolución (régimen antiguo). Es la llave de búsqueda de la SAT. |
| `FechaEmisionDocumentoOrigen` | Debe coincidir día/mes/año con lo registrado en SAT para ese UUID. |
| `MotivoAjuste` | Texto libre del motivo (obligatorio). |
| `RegimenAntiguo` | Solo para referenciar facturas en papel pre-FEL (valor `"Antiguo"`). |
| `SerieDocumentoOrigen` / `NumeroDocumentoOrigen` | **Obligatorios por el XSD** (verificado contra el sandbox de INFILE: sin ellos, rechazo inmediato "attribute is required but missing"). Para origen FEL se toman de la respuesta de certificación del origen — no se derivan del UUID (el sandbox emite serie `**PRUEBAS**`). El doc de reglas solo valida su *contenido* cuando hay `RegimenAntiguo` (contra la resolución y su rango). |
| `Version` | Los ejemplos oficiales usan `"1"`. |

### 1.3 Validaciones que aplica la SAT (rechaza el certificador — sección 3.5)

1. El documento origen debe existir en SAT y estar **Vigente**.
2. El tipo del documento origen debe ser **FACT o FCAM** (no se puede emitir
   NCRE contra FPEQ, RECI, FESP, etc.).
3. El **NIT emisor** de la NCRE debe coincidir con el del documento origen.
4. El **ID receptor** debe coincidir con el del documento origen.
5. La fecha de emisión del origen debe coincidir con la registrada.
6. El certificador puede ser **distinto** al del DTE original (esa validación
   fue dejada sin efecto).
7. **Moneda:** la NCRE debe emitirse en la misma moneda del documento origen
   (validación 2.2.7).
8. **Marca `Exp`:** si el origen era exportación la NCRE debe llevar `Exp="SI"`;
   si no lo era, no debe llevarla (2.2.5 reglas 3-4). Ojo: por catálogo v1.7.6
   el complemento Exportación NO va en la NCRE (código 0) — solo la marca.
9. Cada NCRE referencia **un único DTE**. Para ajustar varios DTE se emiten
   varias NCRE (nota al pie 10 del doc de reglas).

Catálogo de complementos (sección 3.1): el complemento 4 «Referencias de Nota
de crédito y débito» es código **2 = requerido** para NCRE y NDEB, y código
**0 = prohibido** para todos los demás tipos. Todos los demás complementos son
0 para NCRE (incluidos Exportación y Medios de pago).

### 1.4 Frases e impuestos

- Frases (sección 2.6): para NCRE la frase **tipo 1 (retención ISR) es
  requerida**, igual que en FACT; tipos 2, 4, 8 y 9 opcionales. El fallback
  actual del paquete (`CodigoEscenario="1" TipoFrase="1"` cuando no se pasan
  frases) la satisface.
- Impuestos: la NCRE admite los mismos impuestos que la factura (IVA con
  desglose normal). El cálculo es idéntico al de una FACT.

### 1.5 INFILE

No hay endpoint especial: la NCRE se firma y certifica con el mismo POST
unificado que ya usa `InfileProvider::certify()`. El flujo HTTP del paquete no
necesita cambios; todo el trabajo es de generación de XML.

---

## 2. Estado del paquete frente a esto

Lo que ya sirve:

- `DocumentTypeEnum::CREDIT_NOTE = 'NCRE'` existe (`src/Enums/DocumentTypeEnum.php:11`);
  `Tipo="NCRE"` saldría correcto en `dte:DatosGenerales`.
- `TaxCalculatorFactory` y `GeneralTaxCalculator` caen en `default` → IVA 12%,
  que es el tratamiento correcto para la NCRE típica (aunque es un default
  implícito, no una decisión).
- Toda la certificación (`FelCertify` → `FelCertificationService` →
  `InfileProvider`) es agnóstica al tipo: recibe el XML como string.

Bloqueos duros (por orden de esfuerzo):

1. **No existe soporte de Complementos.** Cero ocurrencias de
   `Complemento(s)`/`ReferenciasNota`/`cno:` en `src/`.
   `EmissionDataElement::asXML()` (`src/Xml/Elements/EmissionDataElement.php:46-53`)
   tiene los hijos hardcodeados (generalData → totales) sin ningún punto de
   extensión.
2. **No hay forma de declarar `xmlns:cno`.** `GTDocument` hardcodea los
   namespaces (`src/Xml/Elements/GTDocument.php:26-30`). Alternativa viable:
   declararlo inline en el propio elemento (`xmlns:cno` como atributo literal —
   `XmlDocumentBuilder::buildElement()` escribe cualquier par nombre/valor),
   que es además como lo hacen los ejemplos oficiales.
3. **No hay modelo de referencia al documento origen.** `Invoice` no tiene
   dónde guardar UUID / fecha / motivo / serie / número del DTE original. Lo
   más cercano (`Cancellation`) es de la rama de anulación; `FelOrderData` es
   un huérfano que ningún elemento XML consume.

Y una corrección documental urgente:

- **README ~621-651 («Nota de Crédito (NCRE)»)**: el ejemplo usa `FelAddenda`
  con el namespace del complemento, pero `AdendaElement::asXML()` descarta
  `$namespace` y emite solo `<dte:Adenda><FacturaOriginal>…`. Resultado: NCRE
  sin complemento → rechazo garantizado de SAT («complemento requerido está
  ausente»). Hay que reescribir esa sección cuando exista el soporte real.

Observación colateral: `DocumentTypeEnum::EXPORT_INVOICE = 'FEXP'` no es un
tipo de DTE del catálogo SAT (la exportación es `FACT` con `Exp="SI"` +
complemento Exportaciones). No afecta a NCRE directamente, pero sí a la NCRE de
exportación (regla de la marca `Exp`) y sugiere que el flujo de exportación
tampoco certificaría.

---

## 3. Hallazgos de `hallazgos-revision.md`: ¿cuáles son impedimento?

| # | Hallazgo | ¿Impedimento para NCRE? |
|---|---|---|
| 1 | XML sin escapar (✅ resuelto) | No — al contrario, beneficia: `MotivoAjuste` con `&`/`<` saldrá escapado. |
| 2 | `checkStatus` no envía UUID | **Parcial.** No bloquea emitir, pero sí verificar el estado de la NCRE o del documento origen. (Ya hay un test nuevo `InfileStatusTest.php` que apunta a este fix.) |
| 3 | TypeError en `cancel()` | No bloquea NCRE. Relevante al flujo hermano (recordar: un DTE con NCRE vigente no se puede anular; el error de SAT que lo diga hoy quedaría enmascarado por el TypeError). |
| 4 | `new Invoice()` sin tipo | No — para NCRE siempre se pasa el tipo explícito. |
| 5 | `Cancellation` fecha null | No. |
| 6 | IVA/GranTotal en 0 sin `total` explícito | **SÍ — impedimento real.** Los montos son el corazón de una nota de crédito; con esta regresión una NCRE construida sin `total` explícito saldría con IVA 0 y GranTotal 0 (SAT la rechazaría o, peor, certificaría montos equivocados). El soporte NCRE debe esperar/coordinarse con este fix. |
| 7 | `timeout`/`verify_ssl` muertos | No bloqueante. |
| 8 | `fecha` sin `?? null` | No bloqueante (menor). |

Del backlog menor, dos tocan a NCRE igual que a FACT: la falta de `round()` en
`FelTax`/sumas (riesgo de rechazo por descuadre de centavos) y la zona horaria
del servidor en `FechaHoraEmision` — esta última resuelta el 2026-08-19: el
default ya se sella en `America/Guatemala` (`Support\FelDateTime`).

---

## 4. Diseño propuesto (siguiendo los patrones existentes)

1. **Modelo** `FelReferenceNote` (Models/): `numeroAutorizacionDocumentoOrigen`,
   `fechaEmisionDocumentoOrigen`, `motivoAjuste`, `serieDocumentoOrigen?`,
   `numeroDocumentoOrigen?`, `regimenAntiguo?`, `version = '1'`.
2. **`Invoice`**: propiedad opcional `?FelReferenceNote $referenceNote` (+
   constructor/getter, `toArray()`).
3. **Elementos XML** (Xml/Elements/ + Xml/Enums/): `ComplementsElement` →
   `dte:Complementos` > `dte:Complemento` (IDComplemento, NombreComplemento,
   URIComplemento) > `cno:ReferenciasNota` con `xmlns:cno` inline.
4. **`EmissionDataElement`**: agregar el hijo condicional después de
   `dte:Totales` (mismo patrón que `SATElement` con la adenda).
5. **`CreditNoteGenerator`** (y rama `CREDIT_NOTE`/`DEBIT_NOTE` en
   `DocumentGeneratorFactory`): valida que `referenceNote` exista — si falta,
   `XmlGenerationException` antes de llegar a INFILE. Opcional: validar moneda
   y advertir sobre el plazo de 2 meses (la SAT valida el resto).
6. **Tests**: XML de referencia byte a byte (estilo `factura-referencia.md`)
   con el ejemplo de la sección 1.2; casos: NCRE sin referencia (excepción),
   NCRE con `Exp`, escapado de `MotivoAjuste`.
7. **README**: reemplazar la sección NCRE actual por el uso real.

NDEB (nota de débito) sale casi gratis: mismo complemento, mismas reglas, solo
cambia `Tipo` y `NombreComplemento`.

---

## Fuentes

- SAT, «Reglas y Validaciones aplicables v1.7.6» (jul. 2023) — portal.sat.gob.gt
  (copia local usada para esta investigación; secciones 2.2.5, 2.2.7, 2.6, 2.7,
  3.1, 3.5, 3.13).
- Ejemplos XML oficiales Megaprint: `NOTA DE CREDITO.xml` y
  `Nota de Credito Exportacion.XML` — github.com/IT-Megaprint/Ejemplos-XML-v.2.0
- Portal SAT, Documentación técnica del Régimen FEL —
  portal.sat.gob.gt/portal/documentacion-tecnica-del-regimen-fel/
- Mindy, «Cómo emitir una nota de crédito electrónica (FEL) en Guatemala».
