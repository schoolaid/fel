<?php
namespace SchoolAid\FEL\Contracts;

interface FELAction
{
    public function url(): string;
    public function method(): string;
    public function submit();
}