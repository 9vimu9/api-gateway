<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class GatewayController extends Controller
{
    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function handle(Request $request): JsonResponse
    {
        $requestUri = substr($request->getRequestUri(), 1);
        $target = [];

        foreach (config("app.routes") as $route) {
            if ($route[0] === Str::upper($request->method()) && $requestUri === $route["2"]) {
                $target = $route;
                break;
            }
        }

        if (count($target) === 0) {
            abort(404, "Requested URL Not Found");
        }
        request()->headers->add(['Authorization' => "Bearer {$this->getMicroServiceToken()}"]);
        [$targetMethod, $targetBaseUri, $targetUri] = $target;
        $response = $this->getResponse($targetMethod, $targetBaseUri, $targetUri);
        return response()->json($this->getArrayFromResponse($response), $response->getStatusCode());
    }

    /**
     */
    private function getMicroServiceToken(): string
    {
        try {
            [$targetMethod, $targetBaseUri, $targetUri] = config("app.access_token_validate_route");
            $response = $this->getResponse($targetMethod, $targetBaseUri, $targetUri);
            $responseArray = $this->getArrayFromResponse($response);
            if ($response->getStatusCode() !== 200) {
                abort($response->getStatusCode(), "token validation issue");
            }

            if ($responseArray["valid"] === false) {
                abort(400, "Invalid Authorization Code");
            }
            return $responseArray["token"]["access_token"];
        } catch (GuzzleException|JsonException $exception) {
            abort(500, "something went wrong on request validation");
        }


    }

    /**
     * @param $targetMethod
     * @param $targetBaseUri
     * @param $targetUri
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function getResponse($targetMethod, $targetBaseUri, $targetUri): ResponseInterface
    {

        return (new Client([
            'base_uri' => $targetBaseUri
        ]))->request($targetMethod, $targetUri,
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
    private function getArrayFromResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}
