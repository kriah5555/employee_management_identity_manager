<?php

use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Write code on Method
 *
 * @return response()
 */
if (!function_exists('generateUniqueUsername')) {
    function generateUniqueUsername($username)
    {
        $newUsername = $username;
        $counter = 1;

        while (User::where('username', $newUsername)->exists()) {
            $newUsername = $username . $counter;
            $counter++;
        }

        return $newUsername;
    }
}

if (!function_exists('returnResponse')) {
    function returnResponse($data, $status_code)
    {
        if (array_key_exists('message', $data)) {
            $data['message'] = is_array($data['message']) ? $data['message'] : [$data['message']];
        }
        return response()->json($data, $status_code);
    }
}

if (!function_exists('makeApiRequest')) {
    function makeApiRequest($url, $method = 'GET', $data = [], $headers = [])
    {
        // $url = 'https://dev.indii-2.0.i-manager.infanion.com/api/create-user';
        try {
            // Build the HTTP request
            $request = Http::withHeaders($headers);

            if ($method === 'GET') {
                $response = $request->get($url);
            } elseif ($method === 'POST') {
                $response = $request->post($url, $data);
            } elseif ($method === 'PUT') {
                $response = $request->put($url, $data);
            } elseif ($method === 'DELETE') {
                $response = $request->delete($url);
            } else {
                return null;
            }
            return $response;
        } catch (\Exception $e) {
            throw new Exception("API error");
        }
    }
}

if (!function_exists('microserviceRequest')) {
    function microserviceRequest($route, $method = 'GET', $data = [], $headers = [])
    {
        $apiGatewayUrl = config('app.service_gateway_url');
        $url = $apiGatewayUrl . $route;
        return makeApiRequest($url, $method, $data, $headers);
    }
}

if (!function_exists('returnUnauthorizedResponse')) {
    function returnUnauthorizedResponse($message)
    {
        return returnResponse(
            [
                'success' => false,
                'message' => $message
            ],
            JsonResponse::HTTP_UNAUTHORIZED,
        );
    }
}

if (!function_exists('returnIntenalServerErrorResponse')) {
    function returnIntenalServerErrorResponse($message)
    {
        return returnResponse(
            [
                'success' => false,
                'message' => $message
            ],
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        );
    }
}

if (!function_exists('collectionToValueLabelFormat')) {
    function collectionToValueLabelFormat($collection, $valueKey = 'id', $labelKey = 'name')
    {
        return generateValueLabelArray($collection->toArray(), $valueKey, $labelKey);
    }
}


if (!function_exists('generateValueLabelArray')) {
    function generateValueLabelArray($array, $valueKey = 'id', $labelKey = 'name')
    {
        return array_map(function ($item) use ($valueKey, $labelKey) {
            return [
                'value' => $item[$valueKey],
                'label' => $item[$labelKey],
            ];
        }, $array);
    }
}

if (!function_exists('associativeToDictionaryFormat')) {
    function associativeToDictionaryFormat($associativeArray, $valueKey = 'id', $labelKey = 'name')
    {
        $dict = [];
        foreach ($associativeArray as $key => $value) {
            // Your custom function logic here, which can use both $key and $value.
            $dict[] = [
                $valueKey => $key,
                $labelKey => $value,
            ];
        }
        return $dict;
    }
}