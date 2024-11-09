<?php
namespace SchoolAid\FEL\Models;

class IssuerCredentials
{

    public function __construct(
        public string $infileUser,
        public string $infilePassword,
        public string $infileKey
    ) {}

    public function getInfileUser(): string
    {
        return $this->infileUser;
    }

    public function getInfilePassword(): string
    {
        return $this->infilePassword;
    }

    public function getInfileKey(): string
    {
        return $this->infileKey;
    }

    public function toArray(): array
    {
        return [
            'infileUser' => $this->infileUser,
            'infilePassword' => $this->infilePassword,
            'infileKey' => $this->infileKey
        ];
    }
}
