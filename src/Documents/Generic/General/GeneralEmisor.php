<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\General\IGeneralEmisor;

abstract class GeneralEmisor implements IGeneralEmisor
{
    protected string $correo_emisor;
    protected string $codigo_establecimiento;
    protected string $nit_emisor;
    protected string $nombre_comercial;
    protected string $afiliacion_iva;
    protected string $nombre_emisor;
    protected string $direccion;
    protected string $codigo_postal;
    protected string $municipio;
    protected string $departamento;
    protected string $pais;

    public function __construct(
        string $correo_emisor,
        string $codigo_establecimiento,
        string $nit_emisor,
        string $nombre_comercial,
        string $afiliacion_iva,
        string $nombre_emisor,
        string $direccion,
        string $codigo_postal,
        string $municipio,
        string $departamento,
        string $pais
    ) {
        $this->correo_emisor          = $correo_emisor;
        $this->codigo_establecimiento = $codigo_establecimiento;
        $this->nit_emisor             = $nit_emisor;
        $this->nombre_comercial       = $nombre_comercial;
        $this->afiliacion_iva         = $afiliacion_iva;
        $this->nombre_emisor          = $nombre_emisor;
        $this->direccion              = $direccion;
        $this->codigo_postal          = $codigo_postal;
        $this->municipio              = $municipio;
        $this->departamento           = $departamento;
        $this->pais                   = $pais;
    }
}
