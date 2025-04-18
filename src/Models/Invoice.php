<?php

namespace Schoolaid\Fel\Models;

use DateTimeInterface;
use DateTime;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;

class Invoice
{
    public string $documentType;
    public string $emissionDateTime;
    public string $currencyCode;
    public FelIssuer $issuer;
    public FelReceiver $receiver;
    public FelPhrases $phrases;
    public FelItems $items;
    public FelTotals $totals;
    public ?FelAddenda $addenda;
    public ?FelOrderData $orderData;
    public bool $useTaxes = true;
    
    public function __construct(
        DocumentTypeEnum|string|null $documentType = null,
        string|null $emissionDateTime = null,
        CurrencyEnum|string|null $currencyCode = null,
        ?FelIssuer $issuer = null,
        ?FelReceiver $receiver = null,
        ?FelPhrases $phrases = null,
        ?FelItems $items = null,
        ?FelTotals $totals = null,
        ?FelAddenda $addenda = null,
        ?FelOrderData $orderData = null
    ) {
        // Handle document type
        if ($documentType instanceof DocumentTypeEnum) {
            $this->documentType = $documentType->value;
        } else {
            $this->documentType = $documentType ?? DocumentTypeEnum::getDefault();
        }
        
        // Handle emission date
        $this->emissionDateTime = $emissionDateTime ?? (new DateTime())->format('c');
        
        // Handle currency code
        if ($currencyCode instanceof CurrencyEnum) {
            $this->currencyCode = $currencyCode->value;
        } else {
            $this->currencyCode = $currencyCode ?? CurrencyEnum::getDefault()->value;
        }
        
        // Handle other properties
        $this->issuer = $issuer ?? new FelIssuer();
        $this->receiver = $receiver ?? new FelReceiver();
        $this->phrases = $phrases ?? new FelPhrases();
        $this->items = $items ?? new FelItems();
        $this->totals = $totals ?? new FelTotals();
        $this->addenda = $addenda;
        $this->orderData = $orderData;
    }
    
    public function toArray(): array
    {
        $data = [
            'documentType' => $this->documentType,
            'emissionDateTime' => $this->emissionDateTime,
            'currencyCode' => $this->currencyCode,
            'issuer' => $this->issuer->toArray(),
            'receiver' => $this->receiver->toArray(),
            'phrases' => $this->phrases->toArray(),
            'items' => $this->items->toArray(),
            'totals' => $this->totals->toArray()
        ];
        
        if ($this->addenda) {
            $data['addenda'] = $this->addenda->toArray();
        }
        
        if ($this->orderData) {
            $data['orderData'] = $this->orderData->toArray();
        }
        
        return $data;
    }

    public function setUseTaxes(bool $useTaxes): void
    {
        $this->useTaxes = $useTaxes;
    }

    public function getUseTaxes(): bool
    {
        return $this->useTaxes;
    }
} 