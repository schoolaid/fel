<?php
namespace SchoolAid\FEL\Requests;

use SchoolAid\FEL\Enum\Requests\FELHeaders;
use SchoolAid\FEL\Models\IssuerCredentials;


class FELHeadersRequest
{
    public static function build(IssuerCredentials $credentials): array
    {
        return [
            FELHeaders::UsuarioFirma->value => $credentials->getInfileUser(),
            FELHeaders::LlaveFirma->value   => $credentials->getInfileKey(),
            FELHeaders::UsuarioApi->value   => $credentials->getInfileUser(),
            FELHeaders::LlaveApi->value     => $credentials->getInfilePassword(),
            FELHeaders::Usuario->value      => $credentials->getInfileUser(),
            FELHeaders::Llave->value        => $credentials->getInfileKey(),
            FELHeaders::ContentType->value  => 'application/xml',
        ];
    }
}