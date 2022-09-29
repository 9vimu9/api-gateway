<?php

namespace App\Http\Controllers;

use App\Services\ApiClients\ClientResponse;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use JsonException;

class LoginController extends Controller
{

    /**
     * @throws GuzzleException
     */
    public function login(): JsonResponse
    {
        try {
            [$targetMethod, $targetBaseUri, $targetUri] = config("app.login_route");
            $clientResponse = new ClientResponse($targetBaseUri, $targetMethod, $targetUri);
            $response = $clientResponse->handle();
            return response()->json(
                $clientResponse->getArrayFromResponse($response),
                $response->getStatusCode()
            );
        } catch (Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage(),
                "trace" => (array)$exception
            ], 500);

        }

    }

}
