<?php

namespace App\Http\Controllers;

use App\Services\ApiClients\ClientResponse;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GatewayController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function handle(Request $request): JsonResponse
    {
        try {
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
            $clientResponse = new ClientResponse($targetBaseUri, $targetMethod, $targetUri);
            $response = $clientResponse->handle();
            return response()->json($clientResponse->getArrayFromResponse($response), $response->getStatusCode());
        } catch (Exception $exception) {
            return response()->json([
                "message" => $exception->getMessage(),
                "trace" => (array)$exception
            ], 500);

        }


    }

    /**
     */
    private function getMicroServiceToken(): string
    {
        [$targetMethod, $targetBaseUri, $targetUri] = config("app.access_token_validate_route");
        $clientResponse = new ClientResponse($targetBaseUri, $targetMethod, $targetUri);
        $response = $clientResponse->handle();
        $responseArray = $clientResponse->getArrayFromResponse($response);
        if ($response->getStatusCode() !== 200) {
            abort($response->getStatusCode(), "token validation issue");
        }

        if ($responseArray["valid"] === false) {
            abort(400, "Invalid Authorization Code");
        }
        return $responseArray["token"]["access_token"];

    }
}
