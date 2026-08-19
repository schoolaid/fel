# Factura de referencia (FACT certificada vía INFILE)

DTE real emitido con este paquete el 2026-07-11. Sirve como salida de referencia
para validar que cambios en la generación de XML no alteren el formato.

- Tipo: `FACT` · Moneda: `GTQ`
- Emisor: Stay Hungry, S.A. (NIT 120035502)
- Receptor: CF (Sofia Acevedo)
- Ítem: `2026 - DAY PASS`, Q50.00 (IVA incluido: gravable 44.64 + IVA 5.36)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0"><dte:SAT ClaseDocumento="dte"><dte:DTE ID="DatosCertificados"><dte:DatosEmision ID="DatosEmision"><dte:DatosGenerales FechaHoraEmision="2026-07-11T08:09:21-06:00" CodigoMoneda="GTQ" Tipo="FACT"/>
<dte:Emisor CorreoEmisor="hsinglic@gmail.com" CodigoEstablecimiento="1" NITEmisor="120035502" NombreComercial="Stay Hungry" AfiliacionIVA="GEN" NombreEmisor="Stay Hungry, S.A."><dte:DireccionEmisor>
    <dte:Direccion>Centro comercial Parador Santa Lucía, Ancla 7, Bodega D, 2Da Avenida 5-29 Caserío Matilandia Zona 2</dte:Direccion>
    <dte:CodigoPostal>01010</dte:CodigoPostal>
    <dte:Municipio>SANTA LUCÍA MILPAS ALTAS</dte:Municipio>
    <dte:Departamento>SACATEPEQUEZ</dte:Departamento>
    <dte:Pais>GT</dte:Pais>
</dte:DireccionEmisor>
</dte:Emisor>
<dte:Receptor IDReceptor="CF" NombreReceptor="Sofia Acevedo" CorreoReceptor="sofia.mua99@gmail.com"><dte:DireccionReceptor>
    <dte:Direccion>Ciudad</dte:Direccion>
    <dte:CodigoPostal>01010</dte:CodigoPostal>
    <dte:Municipio>Mixco</dte:Municipio>
    <dte:Departamento>Guatemala</dte:Departamento>
    <dte:Pais>GT</dte:Pais>
</dte:DireccionReceptor>
</dte:Receptor>
<dte:Frases><dte:Frase CodigoEscenario="2" TipoFrase="1"/>
</dte:Frases>
<dte:Items><dte:Item NumeroLinea="1" BienOServicio="S">
    <dte:Cantidad>1</dte:Cantidad>
    <dte:UnidadMedida>UND</dte:UnidadMedida>
    <dte:Descripcion>2026 - DAY PASS</dte:Descripcion>
    <dte:PrecioUnitario>50</dte:PrecioUnitario>
    <dte:Precio>50</dte:Precio>
    <dte:Descuento>0</dte:Descuento>
    <dte:Impuestos><dte:Impuesto>
    <dte:NombreCorto>IVA</dte:NombreCorto>
    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
    <dte:MontoGravable>44.64</dte:MontoGravable>
    <dte:MontoImpuesto>5.36</dte:MontoImpuesto>
</dte:Impuesto>
</dte:Impuestos>
    <dte:Total>50</dte:Total>
</dte:Item>
</dte:Items>
<dte:Totales>
    <dte:TotalImpuestos><dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="5.36"/>
</dte:TotalImpuestos>
    <dte:GranTotal>50</dte:GranTotal>
</dte:Totales>
</dte:DatosEmision>
</dte:DTE>
<dte:Adenda>
    <Orden>Orden #14068, Sofia, Acevedo - julio 2026</Orden>
</dte:Adenda>
</dte:SAT>
</dte:GTDocumento>
```
