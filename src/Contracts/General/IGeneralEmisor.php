<?php
namespace SchoolAid\FEL\Contracts\General;

interface IGeneralEmisor
{
    public function correo_emisor(): string;
    public function codigo_establecimiento(): string;
    public function nit_emisor(): string;
    public function nombre_comercial(): string;
    public function afiliacion_iva(): string;
    public function nombre_emisor(): string;
    public function direccion(): string;
    public function codigo_postal(): string;
    public function municipio(): string;
    public function departamento(): string;
    public function pais(): string;
}
