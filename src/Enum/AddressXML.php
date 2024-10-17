<?php
namespace SchoolAid\FEL\Enum;

enum AddressXML: string {
    case Address    = 'dte:Direccion';
    case PostalCode = 'dte:CodigoPostal';
    case City       = 'dte:Municipio';
    case State      = 'dte:Departamento';
    case Country    = 'dte:Pais';
}
