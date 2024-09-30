<?php
namespace SchoolAid\FEL\Enum;

enum ItemXML: string {
    case Tag               = 'dte:Item';
    case LineNumber        = 'dte:NumeroLinea';
    case ProductOrService  = 'dte:BienOServicio';
    case Quantity          = 'dte:Cantidad';
    case UnitOfMeasurement = 'dte:UnidadMedida';
    case Description       = 'dte:Descripcion';
    case UnitPrice         = 'dte:PrecioUnitario';
    case Price             = 'dte:Precio';
    case Discount          = 'dte:Descuento';
    case Total             = 'dte:Total';
    case TaxDetail         = 'dte:Impuestos';
}
