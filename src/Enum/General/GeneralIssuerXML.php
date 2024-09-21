<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralIssuerXML: string
{
    case EmailIssuer = 'CorreoEmisor';
    case IssuerNit = 'NITEmisor';
    case CommercialName = 'NombreComercial';
    case IssuerName = 'NombreEmisor';
    case IVAAffiliation = 'TipoRegimen';
    case Address = 'DireccionEmisor';
}