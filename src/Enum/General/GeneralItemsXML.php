<?php 

namespace SchoolAid\FEL\Enum\General;


enum GeneralItemsXML: string 
{
    //Main Element
    case Tag = 'dte:Item';
    //All of these are Sub elements
    case LineNumber = 'dte:NumeroLinea';
    case ProductOrService = 'dte:BienOServicio';
    case Quantity = 'dte:Cantidad';
    case UnitOfMeasurement = 'dte:UnidadMedida';
    case Description = 'dte:Descripcion';
    case UnitPrice = 'dte:PrecioUnitario';
    case Price = 'dte:Precio';
    case Discount = 'dte:Descuento';
    case Total = 'dte:Total';

    case Product = 'B';
    case Service = 'S';
}