<?php
namespace SchoolAid\FEL\Enum\Requests;

enum FELHeaders: string {
    case UsuarioFirma = 'UsuarioFirma';
    case LlaveFirma   = 'LlaveFirma';
    case UsuarioApi   = 'UsuarioApi';
    case LlaveApi     = 'LlaveApi';
    case Usuario      = 'Usuario';
    case Llave        = 'Llave';
    case ContentType  = 'content-type';
}
