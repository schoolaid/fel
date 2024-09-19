<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\General\IGeneralEmitter;

abstract class GeneralEmitter implements IGeneralEmitter
{
    protected string $emitter_email;
    protected string $establishment_code;
    protected string $emitter_nit;
    protected string $commercial_name;
    protected string $vat_affiliation;
    protected string $emitter_name;
    protected string $address;
    protected string $postal_code;
    protected string $municipality;
    protected string $department;
    protected string $country;

    public function __construct(
        string $emitter_email,
        string $establishment_code,
        string $emitter_nit,
        string $commercial_name,
        string $vat_affiliation,
        string $emitter_name,
        string $address,
        string $postal_code,
        string $municipality,
        string $department,
        string $country
    ) {
        $this->emitter_email          = $emitter_email;
        $this->establishment_code    = $establishment_code;
        $this->emitter_nit            = $emitter_nit;
        $this->commercial_name            = $commercial_name;
        $this->vat_affiliation       = $vat_affiliation;
        $this->emitter_name           = $emitter_name;
        $this->address               = $address;
        $this->postal_code           = $postal_code;
        $this->municipality          = $municipality;
        $this->department            = $department;
        $this->country               = $country;
    }
}
