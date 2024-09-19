<?php
namespace SchoolAid\FEL\Contracts\General;

interface IGeneralEmitter
{
    public function emitter_email(): string;
    public function establishment_code(): string;
    public function emitter_nit(): string;
    public function commercial_name(): string;
    public function vat_affiliation(): string;
    public function emitter_name(): string;
    public function address(): string;
    public function postal_code(): string;
    public function municipality(): string;
    public function department(): string;
    public function country(): string;
}
