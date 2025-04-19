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

- PHP 8.1 o superior
- Laravel 9.0 o superior
- Extensión XML de PHP habilitada

## Instalación

Puedes instalar el paquete vía composer:
```bash
  composer require schoolaid/fel:dev-develop
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
$uuid = $response->getUuid();
$serie = $response->getSerial();
$numero = $response->getNumber();
$fecha = $response->getCertificationDate();
```
