<?php
namespace SchoolAid\FEL\Requests;

use GuzzleHttp\Client;
use SchoolAid\FEL\Models\IssuerCredentials;
use SchoolAid\FEL\Requests\FELHeadersRequest;

class FELClientRequest
{
    /*
    * @var SchoolAid\FEL\Requests\FELClientRequest $instance
    */
    public static FELClientRequest $instance;

     /*
     * @var GuzzleHttp\Client $client Guzzle client for connection to Infile
     */
    private Client $client;

    /*
     * Static function to return the singleton instance of this class
     * @return SchoolAid\FEL\Requests\FELClientRequest
     */
    public static function getInstance(IssuerCredentials $credentials): FELClientRequest
    {
        if (!isset(self::$instance)) {
            $instance = new static($credentials);
        }

        return $instance;
    }

    public function __construct(IssuerCredentials $credentials)
    {
        $felProcesoURL = "https://certificador.feel.com.gt/fel/";
        $headers = FELHeadersRequest::build($credentials);
        
        $this->client  = new Client([
            'base_uri' => $felProcesoURL,
            'headers' => $headers,
        ]);
    }

    public function client()
    {
        return $this->client;
    }
}