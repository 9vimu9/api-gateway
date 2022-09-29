<?php

namespace App\Services\ApiClients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class ClientResponse
{
    private string $targetBaseUri;
    private string $targetMethod;
    private string $targetUri;
    public function __construct($targetBaseUri,$targetMethod,$targetUri)
    {
        $this->targetBaseUri = $targetBaseUri;
        $this->targetMethod = $targetMethod;
        $this->targetUri = $targetUri;
    }

    /**
     * @throws GuzzleException
     */
    public function handle(): ResponseInterface
    {
        return (new Client([
            'base_uri' => $this->targetBaseUri
        ]))->request($this->targetMethod, $this->targetUri,
            [
                RequestOptions::HEADERS => request()->header(),
                RequestOptions::JSON => request()->all(),
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
    }

    /**
     * @throws JsonException
     */
    public function getArrayFromResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

}
