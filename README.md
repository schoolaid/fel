# Laravel FEL Guatemala

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schoolaid/fel.svg?style=flat-square)](https://packagist.org/packages/schoolaid/fel)
[![Total Downloads](https://img.shields.io/packagist/dt/schoolaid/fel.svg?style=flat-square)](https://packagist.org/packages/schoolaid/fel)

Este paquete proporciona una implementación completa para la Factura Electrónica en Línea (FEL) de Guatemala, conforme a los requerimientos de la SAT. Permite generar, certificar y gestionar facturas electrónicas de manera sencilla en aplicaciones Laravel.

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
   - **Usuario (username)**: Usuario para autenticación
   - **Llave API (API Key)**: Clave para firma de documentos
   - **Llave de Firma (Signature Key)**: Clave adicional de seguridad
   - **NIT Emisor**: Número de Identificación Tributaria de tu empresa

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
FEL_API_KEY=tu_llave_api
FEL_PASSWORD=tu_llave_firma

# NIT del Emisor (opcional, pero recomendado)
FEL_IDENTIFIER=12345678

# Configuración de Conexión
FEL_TIMEOUT=30
FEL_VERIFY_SSL=true
```

#### Opción 2: Archivo de Configuración (config/fel.php)

```php
return [
    'provider' => env('FEL_PROVIDER', 'infile'),
    'username' => env('FEL_USERNAME'),
    'api_key' => env('FEL_API_KEY'),
    'password' => env('FEL_PASSWORD'),
    'identifier' => env('FEL_IDENTIFIER'), // Tu NIT

    'providers' => [
        'infile' => [
            'base_url' => env('FEL_BASE_URL', 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml'),
            'certify_url' => env('FEL_CERTIFY_URL'),
            'cancel_url' => env('FEL_CANCEL_URL'),
            'timeout' => env('FEL_TIMEOUT', 30),
            'verify_ssl' => env('FEL_VERIFY_SSL', true),
        ],
    ],
];
```

#### Opción 3: Configuración Programática (Directa)

Puedes pasar las credenciales directamente sin usar `.env` ni archivos de configuración:

```php
use Schoolaid\Fel\Config\FelConfig;

// Opción 3A: Constructor directo
$config = new FelConfig(
    provider: 'infile',
    username: 'tu_usuario_infile',
    apiKey: 'tu_llave_api',
    signatureKey: 'tu_llave_firma',
    providerConfig: [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => '12345678'  // Tu NIT (opcional)
    ]
);

// Opción 3B: Desde un array
$credenciales = [
    'provider' => 'infile',
    'username' => 'tu_usuario_infile',
    'api_key' => 'tu_llave_api',
    'signature_key' => 'tu_llave_firma',
    'provider_config' => [
        'base_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'certify_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'cancel_url' => 'https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml',
        'timeout' => 30,
        'verify_ssl' => true,
        'identifier' => '12345678'
    ]
];

$config = FelConfig::fromArray($credenciales);

// Opción 3C: Con setters (útil para modificar configuración existente)
$config = new FelConfig();
$config->setProvider('infile')
    ->setUsername('tu_usuario_infile')
    ->setApiKey('tu_llave_api')
    ->setSignatureKey('tu_llave_firma')
    ->setIdentifier('12345678')
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
        'identifier' => $empresa->nit
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
                'identifier' => $company->nit
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
FEL_API_KEY=llave_prueba
FEL_PASSWORD=firma_prueba
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
        'identifier' => '12345678'
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
    $serie = $response->getSerial();
    $numero = $response->getNumber();
    $fecha = $response->getCertificationDate();
    $xmlCertificado = $response->getCertifiedXml();

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

### Nota de Crédito (NCRE)

Para anular o reducir el monto de una factura previamente emitida:

```php
use Schoolaid\Fel\Enums\DocumentTypeEnum;

// Referencia a la factura original
$addendas = [
    new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0',
        'FacturaOriginal',
        'UUID: 12345678-1234-1234-1234-123456789012'
    ),
    new FelAddenda(
        'http://www.sat.gob.gt/face2/ComplementoFacturaEspecial/0.1.0',
        'Motivo',
        'Devolución de mercadería'
    )
];

$invoice = new Invoice(
    DocumentTypeEnum::CREDIT_NOTE,  // NCRE
    now()->format('Y-m-d\TH:i:s'),
    CurrencyEnum::QUETZAL,
    $issuer,
    $receiver,
    $phrases,
    $items,  // Items a acreditar (con montos negativos o positivos según SAT)
    $totals,
    $addendas
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
- `FEL_API_KEY`
- `FEL_PASSWORD`

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
