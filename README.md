# Laravel FEL Guatemala

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schoolaid/fel.svg?style=flat-square)](https://packagist.org/packages/schoolaid/fel)
[![Total Downloads](https://img.shields.io/packagist/dt/schoolaid/fel.svg?style=flat-square)](https://packagist.org/packages/schoolaid/fel)

Este paquete proporciona una implementación completa para la Factura Electrónica en Línea (FEL) de Guatemala, conforme a los requerimientos de la SAT. Permite generar, certificar y gestionar facturas electrónicas de manera sencilla en aplicaciones Laravel.

## Documentación

La documentación completa (en inglés) vive en [`docs/`](docs/README.md):
guías de uso (instalación, [credenciales INFILE](docs/guide/infile-credentials.md),
operaciones, impuestos), [arquitectura](docs/architecture.md),
[pruebas](docs/testing.md) y los documentos de seguimiento del paquete.

## Características

- Generación de XML para documentos fiscales electrónicos
- Certificación de documentos con proveedores autorizados por la SAT
- Soporte para múltiples addendas personalizadas
- Validación automática de datos según normativa SAT
- Cálculo automático de impuestos (IVA)
- Soporte para diferentes tipos de documentos (Facturas, Notas de Crédito, etc.)

## Requisitos

- PHP 8.3 o superior
- Laravel 11.0 o superior
- Extensión XML de PHP habilitada

## Instalación

Puedes instalar el paquete vía composer:
```bash
composer require schoolaid/fel:dev-develop
```

Publica el archivo de configuración:
```bash
php artisan vendor:publish --provider="Schoolaid\Fel\FelServiceProvider"
```

## Configuración de Credenciales INFILE

### Obtención de Credenciales

Para utilizar este paquete necesitas credenciales del proveedor certificador INFILE (FEEL). Sigue estos pasos:

1. **Contactar a INFILE/FEEL**
   - Sitio web: https://feel.com.gt
   - Solicita información sobre el servicio de certificación FEL
   - Completa el proceso de registro como empresa

2. **Documentación Requerida**
   - RTU (Registro Tributario Unificado) de la empresa
   - Patente de comercio
   - Representante legal con DPI
   - Autorización de la SAT para emitir facturas electrónicas

3. **Credenciales que Recibirás**
   - **Usuario (username)**: Usuario/prefijo para autenticación (se envía como `UsuarioFirma` y `UsuarioApi`)
   - **Llave de Firma**: llave del firmador, usada para firmar documentos (INFILE la recibe como `llaveFirma`)
   - **Llave API**: llave del API REST (INFILE la recibe como `llaveApi` y `llave`)
   - **NIT Emisor**: Número de Identificación Tributaria de tu empresa

> ⚠️ **Nombres invertidos:** por compatibilidad histórica, en este paquete el
> parámetro `apiKey`/`api_key` es el que viaja como `llaveFirma`, y
> `signatureKey`/`signature_key` es el que viaja como `llaveApi`/`llave`.
> Es decir: **pon la Llave de Firma en `api_key` y la Llave API en
> `signature_key`**. No se "corrige" el cruce porque los consumidores
> existentes del paquete ya compensan; el mapeo está fijado por
> `InfileHeadersTest`.
>
> Para no depender de los nombres invertidos, usa los **nombres veraces**:
> el constructor `FelConfig::forInfile($username, $llaveFirma, $llaveApi)`,
> las variables de entorno `FEL_LLAVE_FIRMA` / `FEL_LLAVE_API`, y los
> accesores `getLlaveFirma()` / `setLlaveFirma()` y `getLlaveApi()` /
> `setLlaveApi()`. Solo el constructor histórico (`apiKey:` / `signatureKey:`)
> conserva los nombres invertidos, por compatibilidad con las apps existentes.

### Configuración en Laravel

**Resumen de Opciones:**

| Opción | Método | Caso de Uso Ideal |
|--------|--------|-------------------|
| 1 | Variables de Entorno (.env) | Una sola empresa, configuración estática |
| 2 | Archivo de configuración | Configuración centralizada, deployment |
| 3 | Configuración Programática | Credenciales dinámicas, sin archivos |
| 4 | Desde Base de Datos | Múltiples empresas, SaaS |
| 5 | Sistema Multi-tenant | Aplicaciones empresariales complejas |

#### Opción 1: Variables de Entorno (.env)

Agrega las siguientes variables a tu archivo `.env`:

```env
# Proveedor de Certificación
FEL_PROVIDER=infile

# URLs de INFILE (Producción)
FEL_BASE_URL=https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml
FEL_CERTIFY_URL=https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml
FEL_CANCEL_URL=https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml

# Credenciales de INFILE
FEL_USERNAME=tu_usuario_infile
FEL_LLAVE_FIRMA=tu_llave_de_firma  # llave del firmador (header llaveFirma)
FEL_LLAVE_API=tu_llave_api         # llave del API REST (headers llaveApi y llave)
# Nombres legados aún soportados: FEL_KEY (llave de firma) y FEL_PASSWORD (llave del API)

# Identificador para el header `identificador` (opcional)
# OJO: INFILE lo usa como id ÚNICO POR TRANSACCIÓN (control de duplicidad);
# prefiere $config->setIdentifier($ordenId) por documento en vez de un valor fijo
FEL_IDENTIFIER=

# Configuración de Conexión
FEL_TIMEOUT=30
FEL_VERIFY_SSL=true
```

#### Opción 2: Archivo de Configuración (config/fel.php)

```php
return [
    'provider' => env('FEL_PROVIDER', 'infile'),
    'username' => env('FEL_USERNAME'),
    'api_key' => env('FEL_LLAVE_FIRMA', env('FEL_KEY')),          // llave del firmador (viaja como llaveFirma)
    'signature_key' => env('FEL_LLAVE_API', env('FEL_PASSWORD')), // llave del API REST (viaja como llaveApi)

    'provider_config' => [
        'base_url' => env('FEL_BASE_URL', 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml'),
        'certify_url' => env('FEL_CERTIFY_URL'),
        'status_url' => env('FEL_STATUS_URL'),
        'cancel_url' => env('FEL_CANCEL_URL'),
        'timeout' => env('FEL_TIMEOUT', 30),
        'verify_ssl' => env('FEL_VERIFY_SSL', true),
        'identifier' => env('FEL_IDENTIFIER'), // id único por transacción — mejor setIdentifier() por documento
    ],
];
```

#### Opción 3: Configuración Programática (Directa)

Puedes pasar las credenciales directamente sin usar `.env` ni archivos de configuración:

```php
use Schoolaid\Fel\Config\FelConfig;

// Opción 3A (recomendada): constructor con nombres veraces
$config = FelConfig::forInfile(
    username: 'tu_usuario_infile',
    llaveFirma: 'tu_llave_de_firma',  // llave del firmador
    llaveApi: 'tu_llave_api',         // llave del API REST
    providerConfig: [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'identifier' => 'orden-0001'  // id único por transacción (opcional)
    ]
);

// Constructor histórico (nombres invertidos, ver nota de credenciales)
$config = new FelConfig(
    provider: 'infile',
    username: 'tu_usuario_infile',
    apiKey: 'tu_llave_de_firma',   // viaja como llaveFirma
    signatureKey: 'tu_llave_api',  // viaja como llaveApi
    providerConfig: [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => 'orden-0001'  // id único por transacción (opcional)
    ]
);

// Opción 3B: Desde un array
$credenciales = [
    'provider' => 'infile',
    'username' => 'tu_usuario_infile',
    'api_key' => 'tu_llave_de_firma',   // viaja como llaveFirma
    'signature_key' => 'tu_llave_api',  // viaja como llaveApi
    'provider_config' => [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => 'orden-0001' // id único por transacción
    ]
];

$config = FelConfig::fromArray($credenciales);

// Opción 3C: Con setters (útil para modificar configuración existente)
$config = new FelConfig();
$config->setProvider('infile')
    ->setUsername('tu_usuario_infile')
    ->setLlaveFirma('tu_llave_de_firma')  // llave del firmador
    ->setLlaveApi('tu_llave_api')         // llave del API REST
    ->setIdentifier('orden-0001')  // id único por transacción (control de duplicidad)
    ->setProviderConfig([
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
    ]);
```

#### Opción 4: Credenciales desde Base de Datos

Si guardas las credenciales de tus clientes en la base de datos:

```php
use Schoolaid\Fel\Config\FelConfig;

// Obtener credenciales de la base de datos
$empresa = Empresa::find($empresaId);

$config = new FelConfig(
    provider: 'infile',
    username: $empresa->fel_username,
    apiKey: $empresa->fel_api_key,
    signatureKey: $empresa->fel_signature_key,
    providerConfig: [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => 'orden-' . $ordenId // id único por transacción, uno distinto por documento
    ]
);

// Usar la configuración para certificar
$certify = new FelCertify($invoice, $config);
$response = $certify->execute();
```

#### Opción 5: Sistema Multi-tenant

Para aplicaciones con múltiples empresas:

```php
use Schoolaid\Fel\Config\FelConfig;

class FelService
{
    public function certifyForCompany(Invoice $invoice, Company $company)
    {
        // Crear configuración específica para esta empresa
        $config = new FelConfig(
            provider: 'infile',
            username: $company->fel_credentials['username'],
            apiKey: $company->fel_credentials['api_key'],
            signatureKey: $company->fel_credentials['signature_key'],
            providerConfig: [
                'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
                'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
                'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
                'timeout' => 30,
                'verify_ssl' => true,
                'identifier' => 'orden-' . $orderId // id único por transacción, uno distinto por documento
            ]
        );

        $certify = new FelCertify($invoice, $config);
        return $certify->execute();
    }
}

// Uso
$felService = new FelService();
$response = $felService->certifyForCompany($invoice, $empresa);
```

### Ambiente de Pruebas

INFILE también ofrece un ambiente de pruebas (sandbox). Para utilizarlo:

```env
# URLs de Prueba (si INFILE las proporciona)
FEL_BASE_URL=https://certificador-sandbox.feel.com.gt/fel/procesounificado/transaccion/v2/xml
FEL_CERTIFY_URL=https://certificador-sandbox.feel.com.gt/fel/procesounificado/transaccion/v2/xml
FEL_CANCEL_URL=https://certificador-sandbox.feel.com.gt/fel/procesounificado/transaccion/v2/xml

# Credenciales de Prueba (proporcionadas por INFILE)
FEL_USERNAME=usuario_prueba
FEL_LLAVE_FIRMA=llave_firma_prueba
FEL_LLAVE_API=llave_api_prueba
```

### Verificación de Configuración

Prueba tu configuración con este código:

```php
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Certification\FelCertificationService;

try {
    // Desde .env
    $config = FelConfig::fromConfig();

    // O directamente
    $config = new FelConfig(
        provider: 'infile',
        username: 'tu_usuario',
        apiKey: 'tu_api_key',
        signatureKey: 'tu_signature_key',
        providerConfig: [
            'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
            'timeout' => 30,
            'verify_ssl' => true
        ]
    );

    $service = new FelCertificationService($config);

    // Si no hay excepciones, la configuración es válida
    echo "Configuración correcta";
} catch (\Exception $e) {
    echo "Error de configuración: " . $e->getMessage();
}
```

### Ejemplo Completo sin usar .env

Aquí un ejemplo completo de certificación pasando credenciales directamente:

```php
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Actions\FelCertify;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelPhrase;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;

// 1. Configurar credenciales directamente (sin .env)
$config = new FelConfig(
    provider: 'infile',
    username: 'mi_usuario_infile',
    apiKey: 'mi_api_key',
    signatureKey: 'mi_signature_key',
    providerConfig: [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => 'orden-0001' // id único por transacción
    ]
);

// 2. Crear la factura
$issuer = new FelIssuer(
    'facturacion@miempresa.com',
    '1',
    '12345678',
    'Mi Empresa S.A.',
    IVAAffiliationTypeEnum::General,
    'Mi Empresa',
    new FelAddress('Avenida Reforma 1-1 Zona 10', '01010', 'Guatemala', 'Guatemala', 'GT')
);

$receiver = new FelReceiver(
    'CF',
    null,
    'Consumidor Final',
    new FelAddress('Ciudad', '01001', 'Guatemala', 'Guatemala', 'GT')
);

$items = new FelItems([
    new FelItem(1, 'B', 100.0, 'UND', 'Producto de prueba', 100.0, 1, 0.0, [], 100.0)
]);

$invoice = new Invoice(
    DocumentTypeEnum::LOCAL_INVOICE,
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    new FelPhrases([new FelPhrase(1, 1)]),
    $items,
    new FelTotals(grandTotal: 100.0)
);

// Si omites la fecha, el paquete la sella en hora de Guatemala (-06:00),
// no en la zona del runtime: `new Invoice(DocumentTypeEnum::LOCAL_INVOICE)`.
// El reloj está en Schoolaid\Fel\Support\FelDateTime si lo necesitas aparte.

// 3. Certificar usando la configuración directa
$certify = new FelCertify($invoice, $config);
$response = $certify->execute();

// 4. Procesar respuesta
if ($response->isSuccessful()) {
    echo "Factura certificada: " . $response->getUuid();
} else {
    echo "Error: " . implode(', ', $response->getErrors());
}
```

## Casos de Uso Comunes

### Factura LOCAL_INVOICE (FACT)
La factura local (FACT) es el tipo de documento más común utilizado para transacciones dentro de Guatemala.

```php
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Models\FelAddenda;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrase;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Actions\FelGenerate;
use Schoolaid\Fel\Actions\FelCertify;
use Schoolaid\Fel\Config\FelConfig;

// Configurar datos básicos
$issuerAddress = new FelAddress(
    'Avenida Reforma 15-85 Zona 10, Edificio Torre Internacional Nivel 11',
    '01001',
    'Guatemala',
    'Guatemala',
    'GT'
);

$issuer = new FelIssuer(
    'facturacion@empresa.com',
    '1',
    '12345678',  // NIT del emisor
    'Empresa, S.A.',
    IVAAffiliationTypeEnum::General,
    'Mi Empresa',
    $issuerAddress
);

$receiverAddress = new FelAddress(
    'Avenida Las Américas 7-62 Zona 13',
    '01013',
    'Guatemala',
    'Guatemala',
    'GT'
);

$receiver = new FelReceiver(
    '87654321',  // NIT del receptor (o CF para Consumidor Final)
    'cliente@cliente.com',
    'Cliente Frecuente, S.A.',
    $receiverAddress
);

// Frases requeridas para FACT
$phrases = new FelPhrases([
    new FelPhrase(1, 1)  // Frase 1 y Escenario 1 - Afecta IVA
]);

// Productos/servicios
$items = new FelItems([
    new FelItem(
        1,              // Número de línea
        'B',            // Bien (B) o Servicio (S)
        500.0,          // Precio unitario sin IVA
        'UND',          // Unidad de medida
        'Computadora portátil HP Probook 450 G8', // Descripción
        500.0,          // Precio (sin impuestos)
        1,              // Cantidad
        0.0,            // Descuento
        [],             // Los impuestos se calcularán automáticamente
        500.0           // Total de línea
    ),
    new FelItem(
        2,              // Número de línea
        'S',            // Bien (B) o Servicio (S)
        200.0,          // Precio unitario sin IVA
        'UND',          // Unidad de medida
        'Servicio de instalación y configuración', // Descripción
        200.0,          // Precio (sin impuestos)
        1,              // Cantidad
        0.0,            // Descuento
        [],             // Los impuestos se calcularán automáticamente
        200.0           // Total de línea
    )
]);

// Totales con IVA
$totals = new FelTotals(
    grandTotal: 700.0   // Total incluyendo impuestos (los demás valores se calcularán automáticamente)
);

// Addendas (información adicional)
$addendas = [
    new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0',
        'ReferenciaInterna',
        'Orden #FT-2023-1234'
    ),
    new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0',
        'DatosCliente',
        'Proyecto: Implementación ERP'
    )
];

// Crear la factura (FACT)
$invoice = new Invoice(
    DocumentTypeEnum::LOCAL_INVOICE,  // FACT - Factura local
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    $phrases,
    $items,
    $totals,
    $addendas
);

// Certificar
$config = FelConfig::fromConfig();
$certify = new FelCertify($invoice, $config);
$response = $certify->execute();

// Procesar respuesta
if ($response->isSuccessful()) {
    $uuid = $response->getUuid();
    $serie = $response->getSeries();
    $numero = $response->getNumber();
    $fecha = $response->getCertificationDate();
    // INFILE devuelve el XML certificado en base64: decodifícalo antes de
    // guardarlo como archivo .xml.
    $xmlCertificado = base64_decode($response->getCertifiedXml());

    // Guardar los datos en tu base de datos
    echo "Factura certificada exitosamente: {$serie}-{$numero}";
} else {
    $errores = $response->getErrors();
    echo "Error al certificar: " . implode(', ', $errores);
}
```

### Factura a Consumidor Final (CF)

Para ventas a consumidores finales sin NIT:

```php
// El receptor debe tener 'CF' como ID
$receiver = new FelReceiver(
    'CF',  // Consumidor Final
    null,  // Email opcional
    'Consumidor Final',
    $receiverAddress
);

// El resto es igual a una factura normal
$invoice = new Invoice(
    DocumentTypeEnum::LOCAL_INVOICE,
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    $phrases,
    $items,
    $totals
);
```

### Factura de Exportación (FEXP)

Para exportaciones, no se aplica IVA:

```php
use Schoolaid\Fel\Enums\DocumentTypeEnum;

// Receptor en el extranjero
$receiver = new FelReceiver(
    'EXPORTACION',  // ID para exportaciones
    'cliente@internacional.com',
    'Cliente Internacional Inc.',
    $receiverAddress
);

// Frases para exportación (sin IVA)
$phrases = new FelPhrases([
    new FelPhrase(2, 1)  // Frase 2 - Exento de IVA
]);

// Items (el precio es el total, no se calcula IVA)
$items = new FelItems([
    new FelItem(
        1,
        'B',
        1000.0,  // Precio = Total (sin IVA)
        'UND',
        'Producto para exportación',
        1000.0,
        1,
        0.0,
        [],
        1000.0
    )
]);

$totals = new FelTotals(grandTotal: 1000.0);

// Crear factura de exportación
$invoice = new Invoice(
    DocumentTypeEnum::EXPORT_INVOICE,  // FEXP
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::DOLLAR,  // Puede ser USD para exportaciones
    $issuer,
    $receiver,
    $phrases,
    $items,
    $totals
);
```

### Nota de Crédito (NCRE) y Nota de Débito (NDEB)

Para ajustar (devolución, descuento, corrección) una factura ya certificada.
La SAT exige que toda NCRE/NDEB incluya el complemento **ReferenciasNota** con
los datos del documento origen; el paquete lo genera a partir de un
`FelReferenceNote` (sin él, `generateXml()` lanza `XmlGenerationException`):

```php
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelReferenceNote;

// Referencia a la factura original (obligatoria). Los cinco datos vienen del
// CertificationResponse con que se certificó el DTE origen: getUuid(),
// getSeries() y getNumber() (serie y número NO se derivan del UUID; el
// sandbox de INFILE, por ejemplo, emite la serie "**PRUEBAS**").
$reference = new FelReferenceNote(
    '12345678-1234-1234-1234-123456789012', // UUID (autorización) del DTE origen
    '2025-03-15',                           // Fecha de emisión del DTE origen
    'Devolución de mercadería',             // Motivo del ajuste
    'A1B2C3D4',                             // Serie del DTE origen (getSeries)
    '1234567890'                            // Número del DTE origen (getNumber)
);

$invoice = new Invoice(
    DocumentTypeEnum::CREDIT_NOTE,  // NCRE (para NDEB usa DEBIT_NOTE)
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,          // Debe ser la misma moneda del DTE origen
    $issuer,                        // Mismo NIT emisor que el DTE origen
    $receiver,                      // Mismo receptor que el DTE origen
    $phrases,
    $items,                         // Items a acreditar (montos positivos)
    $totals
);
$invoice->setReferenceNote($reference);
```

Esto emite dentro de `dte:DatosEmision`, después de `dte:Totales`:

```xml
<dte:Complementos>
    <dte:Complemento IDComplemento="1" NombreComplemento="NOTA CREDITO"
                     URIComplemento="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0">
        <cno:ReferenciasNota xmlns:cno="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0"
            Version="1"
            NumeroAutorizacionDocumentoOrigen="12345678-1234-1234-1234-123456789012"
            SerieDocumentoOrigen="A1B2C3D4"
            NumeroDocumentoOrigen="1234567890"
            FechaEmisionDocumentoOrigen="2025-03-15"
            MotivoAjuste="Devolución de mercadería"/>
    </dte:Complemento>
</dte:Complementos>
```

El complemento **solo** es válido en NCRE y NDEB: si se adjunta un
`FelReferenceNote` a cualquier otro tipo (FACT, FEXP, RDON…),
`generateXml()` lanza `XmlGenerationException` antes de llegar al
certificador, porque la SAT lo rechaza con el error 31101 («El complemento
[ReferenciasNota] con prefijo [cno] no es valido para el tipo de documento
[FACT]»).

Reglas SAT a tener en cuenta (las valida el certificador, rechazo si no se
cumplen): el DTE origen debe existir, estar vigente y ser FACT o FCAM; NIT
emisor, receptor, fecha y moneda deben coincidir con el origen; cada nota
referencia **un solo** DTE; y un DTE con nota vigente asociada ya no puede
anularse. La Ley del IVA da 2 meses desde la factura para que el ajuste
conserve el derecho a crédito fiscal (pasado el plazo la SAT certifica igual).

Sí se admiten **varias notas sobre la misma factura** (p. ej. una NCRE parcial
y luego otra por el saldo): verificado contra el sandbox de INFILE.

Cuando la SAT rechaza, `$response->getErrors()` trae el detalle real de cada
validación (`descripcion_errores` de INFILE), no el genérico «Existen errores
en la validacion del XML»:

```php
$response = (new FelCertify($creditNote, $config))->execute();

if (! $response->isSuccessful()) {
    foreach ($response->getErrors() as $error) {
        // FEL-GUI-51 | 3.5 | 3.5.1 | No. 5 | Error - El valor de la casilla
        // ID del Receptor no coincide con el registrado en el Documento Origen.
        logger()->error($error);
    }
}
```

Para referenciar una factura **en papel del régimen antiguo** (pre-FEL):

```php
$reference = new FelReferenceNote(
    '1364585227',        // Número de resolución de autorización
    '2018-05-20',
    'Anulación parcial',
    '5AAE0F7A',          // Serie del documento origen
    '1364585227',        // Número del documento origen
    oldRegime: true      // Emite RegimenAntiguo="Antiguo"
);
```

### Factura de Pequeño Contribuyente (FPEQ)

Para contribuyentes en el régimen de pequeño contribuyente:

```php
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;

// Emisor pequeño contribuyente
$issuer = new FelIssuer(
    'facturacion@pequeno.com',
    '1',
    '12345678',
    'Pequeño Negocio',
    IVAAffiliationTypeEnum::PEQ,  // Pequeño contribuyente
    'Mi Pequeño Negocio',
    $issuerAddress
);

// Frases para pequeño contribuyente
$phrases = new FelPhrases([
    new FelPhrase(4, 1)  // Frase específica para pequeños contribuyentes
]);

$invoice = new Invoice(
    DocumentTypeEnum::SMALL_TAXPAYER_INVOICE,  // FPEQ
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    $phrases,
    $items,
    $totals
);
```

### Recibo de Donación (RDON)

Para organizaciones sin fines de lucro:

```php
use Schoolaid\Fel\Enums\DocumentTypeEnum;

$phrases = new FelPhrases([
    new FelPhrase(3, 1)  // Frase para donaciones
]);

$invoice = new Invoice(
    DocumentTypeEnum::DONATION_RECEIPT,  // RDON
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    $phrases,
    $items,
    $totals
);
```

## Operaciones Avanzadas

### Cancelar una Factura

```php
use Schoolaid\Fel\Actions\FelCancel;
use Schoolaid\Fel\Models\Cancellation;

$cancellation = new Cancellation(
    uuid: '12345678-1234-1234-1234-123456789012',  // UUID de la factura a cancelar
    nitIssuer: '12345678',  // NIT del emisor
    idReceiver: '87654321',  // NIT o CF del receptor
    reason: 'Anulación por devolución de mercadería',
    dateTime: now()->format('Y-m-d\TH:i:s')
);

$config = FelConfig::fromConfig();
$cancelAction = new FelCancel($cancellation, $config);
$response = $cancelAction->execute();

if ($response->isSuccessful()) {
    echo "Factura cancelada exitosamente";
} else {
    echo "Error: " . implode(', ', $response->getErrors());
}
```

### Generar XML sin Certificar

Si solo necesitas generar el XML sin enviarlo a INFILE:

```php
use Schoolaid\Fel\Actions\FelGenerate;

$generate = new FelGenerate($invoice);
$xml = $generate->generateXml();

// Guardar el XML o procesarlo según necesites
file_put_contents('factura.xml', $xml);
```

### Manejo de Errores

```php
use Schoolaid\Fel\Certification\Exceptions\CertificationException;
use Schoolaid\Fel\Certification\Exceptions\AuthenticationException;

try {
    $certify = new FelCertify($invoice, $config);
    $response = $certify->execute();

    if ($response->isSuccessful()) {
        // Éxito
        $uuid = $response->getUuid();
    } else {
        // Errores de validación de INFILE
        foreach ($response->getErrors() as $error) {
            \Log::error("Error FEL: {$error}");
        }
    }
} catch (AuthenticationException $e) {
    // Error de autenticación con INFILE
    \Log::error("Error de autenticación: " . $e->getMessage());
} catch (CertificationException $e) {
    // Otros errores de certificación
    \Log::error("Error de certificación: " . $e->getMessage());
} catch (\Exception $e) {
    // Errores generales
    \Log::error("Error general: " . $e->getMessage());
}
```

## Tipos de Documentos Soportados

| Tipo de Documento | Enum | Código SAT | Descripción |
|-------------------|------|------------|-------------|
| Factura Local | `DocumentTypeEnum::LOCAL_INVOICE` | FACT | Factura estándar con IVA |
| Factura de Cambio | `DocumentTypeEnum::EXCHANGE_INVOICE` | FCAM | Factura de cambio |
| Factura de Exportación | `DocumentTypeEnum::EXPORT_INVOICE` | FEXP | Factura sin IVA para exportaciones |
| Factura Especial | `DocumentTypeEnum::SPECIAL_INVOICE` | FESP | Factura especial |
| Nota de Crédito | `DocumentTypeEnum::CREDIT_NOTE` | NCRE | Anulación o reducción de factura |
| Nota de Débito | `DocumentTypeEnum::DEBIT_NOTE` | NDEB | Aumento de factura |
| Nota de Abono | `DocumentTypeEnum::CREDIT_MEMO` | NABN | Nota de abono |
| Recibo | `DocumentTypeEnum::RECEIPT` | RECI | Recibo genérico |
| Recibo de Donación | `DocumentTypeEnum::DONATION_RECEIPT` | RDON | Recibo para donaciones |
| Factura Pequeño Contribuyente | `DocumentTypeEnum::SMALL_TAXPAYER_INVOICE` | FPEQ | Factura régimen pequeño contribuyente |

## Impuestos Soportados

| Impuesto | Enum | Tasa | Descripción |
|----------|------|------|-------------|
| IVA | `TaxEnum::IVA` | 12% | Impuesto al Valor Agregado |
| ISO | `TaxEnum::ISO` | 1% | Impuesto de Solidaridad |
| RETIVA | `TaxEnum::RETIVA` | 1% | Retención de IVA |
| ISR | `TaxEnum::ISR` | 1% | Impuesto Sobre la Renta |

## Frases y Escenarios SAT

Las frases indican el régimen fiscal aplicable:

```php
// Frase 1: Sujeto a pagos trimestrales de IVA
new FelPhrase(1, 1)

// Frase 2: Exento de IVA (exportaciones)
new FelPhrase(2, 1)

// Frase 3: Donaciones
new FelPhrase(3, 1)

// Frase 4: Pequeño contribuyente
new FelPhrase(4, 1)
```

## Solución de Problemas

### Error de Autenticación

```
Error: Usuario o contraseña incorrectos
```

**Solución:** Verifica que tus credenciales en `.env` sean correctas:
- `FEL_USERNAME`
- `FEL_LLAVE_FIRMA` (llave del firmador — viaja como `llaveFirma`)
- `FEL_LLAVE_API` (llave del API REST — viaja como `llaveApi`)

Si usas los nombres legados, recuerda que están invertidos: la llave de firma
va en `FEL_KEY` y la llave del API en `FEL_PASSWORD` (ver la nota en
"Configuración de Credenciales INFILE").

### Error de Validación SAT

```
Error: El NIT del receptor no es válido
```

**Solución:** Verifica que el NIT tenga el formato correcto (sin guiones) o usa "CF" para consumidor final.

### Timeout de Conexión

```
Error: Connection timeout
```

**Solución:** Aumenta el timeout en tu configuración:
```env
FEL_TIMEOUT=60
```

### Error de SSL

```
Error: SSL certificate problem
```

**Solución:** Si estás en desarrollo, puedes deshabilitar la verificación SSL (NO recomendado para producción):
```env
FEL_VERIFY_SSL=false
```

## Licencia

MIT License

## Soporte

Para reportar bugs o solicitar nuevas funcionalidades, por favor abre un issue en el repositorio del proyecto```
