<?php
namespace SchoolAid\FEL\Requests;

use Illuminate\Support\Facades\Config;
use SchoolAid\FEL\Enum\Requests\FELHeaders;
use SchoolAid\FEL\Models\IssuerCredentials;

// class FELHeadersRequest
// {
//     public static function build(): array
//     {
//         $usuarioFirma = Config::get('fel.UsuarioFirma');
//         $llaveFirma   = Config::get('fel.llaveFirma');
//         $usuarioApi   = Config::get('fel.UsuarioApi');
//         $llaveApi     = Config::get('fel.llaveApi');
//         $usuario      = Config::get('fel.Usuario');
//         $llave        = Config::get('fel.llave');

//         return [
//             FELHeaders::UsuarioFirma->value => $usuarioFirma,
//             FELHeaders::LlaveFirma->value   => $llaveFirma,
//             FELHeaders::UsuarioApi->value   => $usuarioApi,
//             FELHeaders::LlaveApi->value     => $llaveApi,
//             FELHeaders::Usuario->value      => $usuario,
//             FELHeaders::Llave->value        => $llave,
//             FELHeaders::ContentType->value  => 'application/xml',
//         ];
//     }
// }

class FELHeadersRequest
{
    public static function build(IssuerCredentials $credentials): array
    {
        return [
            FELHeaders::UsuarioFirma->value => $credentials->getInfileUser(),
            FELHeaders::LlaveFirma->value   => $credentials->getInfilePassword(),
            FELHeaders::UsuarioApi->value   => $credentials->getInfileUser(),
            FELHeaders::LlaveApi->value     => $credentials->getInfileKey(),
            FELHeaders::Usuario->value      => $credentials->getInfileUser(),
            FELHeaders::Llave->value        => $credentials->getInfilePassword(),
            FELHeaders::ContentType->value  => 'application/xml',
        ];
    }
}