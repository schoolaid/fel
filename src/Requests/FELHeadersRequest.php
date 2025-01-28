<?php
namespace SchoolAid\FEL\Requests;

use SchoolAid\FEL\Enum\Requests\FELHeaders;
use SchoolAid\FEL\Models\IssuerCredentials;


class FELHeadersRequest
{
    public static function build(IssuerCredentials $credentials): array
    {
        return [
            FELHeaders::UsuarioFirma->value => $credentials->getUserSignature(),
            FELHeaders::LlaveFirma->value   => $credentials->getKeySignature(),
            FELHeaders::UsuarioApi->value   => $credentials->getUserApi(),
            FELHeaders::LlaveApi->value     => $credentials->getKeyApi(),
            FELHeaders::Usuario->value      => $credentials->getUser(),
            FELHeaders::Llave->value        => $credentials->getKey(),
            FELHeaders::ContentType->value  => 'application/xml',
        ];
    }
}