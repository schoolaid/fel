<?php
namespace SchoolAid\FEL\Enum;

enum AddendaXML: string {
    case Tag = 'dte:Adenda';
    case Value = 'dte:Valor';
    case Name = 'dte:Nombre';
}