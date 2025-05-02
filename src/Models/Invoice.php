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
    public array $addendas = [];
    public ?FelOrderData $orderData;
    public bool $useTaxes = true;
    public ?string $personType = null;
    
    public function __construct(
        DocumentTypeEnum|string|null $documentType = null,
        string|null $emissionDateTime = null,
        CurrencyEnum|string|null $currencyCode = null,
        ?FelIssuer $issuer = null,
        ?FelReceiver $receiver = null,
        ?FelPhrases $phrases = null,
        ?FelItems $items = null,
        ?FelTotals $totals = null,
        FelAddenda|array|null $addendas = null,
        ?FelOrderData $orderData = null,
        ?string $personType = null
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
        
        // Handle addendas - can be a single FelAddenda, array of FelAddenda objects, or null
        if ($addendas instanceof FelAddenda) {
            $this->addendas = [$addendas];
        } elseif (is_array($addendas)) {
            $this->addendas = $addendas;
        }
        
        $this->orderData = $orderData;
        $this->personType = $personType;
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
        
        if (!empty($this->addendas)) {
            $data['addendas'] = array_map(function(FelAddenda $addenda) {
                return $addenda->toArray();
            }, $this->addendas);
        }
        
        if ($this->orderData) {
            $data['orderData'] = $this->orderData->toArray();
        }
        
        if ($this->personType !== null) {
            $data['personType'] = $this->personType;
        }
        
        return $data;
    }

    /**
     * Add an addendum to the invoice
     *
     * @param FelAddenda $addenda
     * @return self
     */
    public function addAddenda(FelAddenda $addenda): self
    {
        $this->addendas[] = $addenda;
        return $this;
    }
    
    /**
     * Get all addendas
     * 
     * @return array
     */
    public function getAddendas(): array
    {
        return $this->addendas;
    }
    
    /**
     * Check if the invoice has any addendas
     *
     * @return bool
     */
    public function hasAddendas(): bool
    {
        return !empty($this->addendas);
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