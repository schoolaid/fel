<?php
namespace SchoolAid\FEL\Actions\Interfaces;

use GuzzleHttp\Psr7\Response;
use SchoolAid\FEL\Contracts\FELAction;
use SchoolAid\FEL\Requests\FELClientRequest;

abstract class FELCertAction implements FELAction
{
    public string $method = 'POST';
    private string $body;

    public function method(): string
    {
        return $this->method;
    }

    public static $instance;

    /*
     * Static function to return the singleton instance of this class
     * @return SchoolAid\FEL\Actions\Interfaces\FELCertAction
     */
    public static function getInstance()
    {
        if (!isset($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    protected function parseBody(Response $response)
    {
        $bodyContent = $response->getBody()->getContents();
        if ($response->getStatusCode() === 200) {
            return $bodyContent;
        }
    }

    public function submit()
    {

        $client   = FELClientRequest::getInstance()->client();
        $response = $client->request($this->method(), $this->url(), [
            'body' => $this->body,
        ]);

        return [
            'code' => $response->getStatusCode(),
            'body' => $this->parseBody($response),
        ];
    }

    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }
}
