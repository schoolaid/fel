<?php
namespace SchoolAid\FEL\Documents;

class InvoiceServiceResponse
{
    public function __construct(public bool $success, public string $message, public ?array $data)
    {
        
    }

    public function format()
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}
