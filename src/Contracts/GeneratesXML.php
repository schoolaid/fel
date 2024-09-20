<?php 

namespace SchoolAid\FEL\Contracts;


interface GeneratesXML
{
    public function asXML(): string;
}