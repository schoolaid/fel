<?php

namespace Schoolaid\Fel\Enums;

enum DocumentTypeEnum: string
{
    case LOCAL_INVOICE = 'FACT';
    case EXCHANGE_INVOICE = 'FCAM';
    case EXPORT_INVOICE = 'FEXP';
    case SPECIAL_INVOICE = 'FESP';
    case CREDIT_NOTE = 'NCRE';
    case DEBIT_NOTE = 'NDEB';
    case CREDIT_MEMO = 'NABN';
    case RECEIPT = 'RECI';
    case DONATION_RECEIPT = 'RDON';
    case SMALL_TAXPAYER_INVOICE = 'FPEQ';

    public static function getDefault(): self
    {
        return self::LOCAL_INVOICE;
    }

    public function getDescription(): string
    {
        return match($this) {
            self::LOCAL_INVOICE => 'Local Invoice',
            self::EXCHANGE_INVOICE => 'Exchange Invoice',
            self::EXPORT_INVOICE => 'Export Invoice',
            self::SPECIAL_INVOICE => 'Special Invoice',
            self::CREDIT_NOTE => 'Credit Note',
            self::DEBIT_NOTE => 'Debit Note',
            self::CREDIT_MEMO => 'Credit Memo',
            self::RECEIPT => 'Receipt',
            self::DONATION_RECEIPT => 'Donation Receipt',
            self::SMALL_TAXPAYER_INVOICE => 'Small Taxpayer Invoice',
        };
    }
}