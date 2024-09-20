<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralAddressXML: string {
    case address = 'dte:Direccion';
    case postal_code = 'dte:CodigoPostal';
    case city = 'dte:Municipio';
    case state = 'dte:Departamento';
    case country = 'dte:Pais';
}