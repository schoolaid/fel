<?php
namespace SchoolAid\FEL\Models;

class IssuerCredentials
{

    public function __construct(
        public string $userSignature,
        public string $keySignature,
        public string $userApi,
        public string $keyApi,
        public string $user,
        public string $key
    ) {}

    public function getUserSignature(): string
    {
        return $this->userSignature;
    }   

    public function getKeySignature(): string
    {
        return $this->keySignature;
    }

    public function getUserApi(): string
    {
        return $this->userApi;
    }

    public function getKeyApi(): string
    {
        return $this->keyApi;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
