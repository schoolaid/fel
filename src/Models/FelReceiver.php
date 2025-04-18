<?php

namespace Schoolaid\Fel\Models;

class FelReceiver
{
    public string $id;
    public string $email;
    public string $name;
    public FelAddress $address;
    
    public function __construct(
        string $id = 'CF',
        string $email = '',
        string $name = '',
        ?FelAddress $address = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->address = $address ?? new FelAddress();
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'address' => $this->address->toArray()
        ];
    }
} 