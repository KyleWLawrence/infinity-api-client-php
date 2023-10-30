<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use KyleWLawrence\Infinity\Api\Exceptions\ApiResponseException;
use KyleWLawrence\Infinity\Api\Exceptions\AuthException;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP functions via curl
 */
class Http
{
    public static $curl;

    /**
     * The function sends an HTTP request using the specified HTTP client and options,
     * and returns the response body as a JSON-decoded object.
     *
     * @param HttpClient client An instance of the HttpClient class.
     * @param endPoint The `endPoint` parameter is a string that represents the specific
     * endpoint or URL path that you want to send the HTTP request to. It is appended to
     * the base URL of the API.
     * @param options The `options` parameter is an array that allows you to customize
     * the behavior of the `send` method. It has the following keys:
     *
     * @return the response body as a JSON-decoded object.
     */
    public static function send(
        HttpClient $client,
        $endPoint,
        $options = []
    ) {
        $options = array_merge(
            [
                'method' => 'GET',
                'contentType' => 'application/json',
                'postFields' => null,
                'queryParams' => null,
            ],
            $options
        );

        $headers = array_merge([
            'Accept' => 'application/json',
            'Content-Type' => $options['contentType'],
            'User-Agent' => $client->getUserAgent(),
        ], $client->getHeaders());

        $request = new Request(
            $options['method'],
            $client->getApiUrl().$client->getApiBasePath().$endPoint,
            $headers
        );

        $requestOptions = [];

        if (! empty($options['multipart'])) {
            $request = $request->withoutHeader('Content-Type');
            $requestOptions['multipart'] = $options['multipart'];
        } elseif (! empty($options['postFields'])) {
            $request = $request->withBody(Utils::streamFor(json_encode($options['postFields'])));
        } elseif (! empty($options['file'])) {
            if ($options['file'] instanceof StreamInterface) {
                $request = $request->withBody($options['file']);
            } elseif (is_file($options['file'])) {
                $fileStream = new LazyOpenStream($options['file'], 'r');
                $request = $request->withBody($fileStream);
            }
        }

        if (! empty($options['queryParams'])) {
            foreach ($options['queryParams'] as $queryKey => $queryValue) {
                $uri = $request->getUri();
                $uri = $uri->withQueryValue($uri, $queryKey, (string) $queryValue);
                $request = $request->withUri($uri, true);
            }
        }

        try {
            [$request, $requestOptions] = $client->getAuth()->prepareRequest($request, $requestOptions);
            $response = $client->guzzle->send($request, $requestOptions);
        } catch (RequestException $e) {
            $requestException = RequestException::create($e->getRequest(), $e->getResponse(), $e);
            throw new ApiResponseException($requestException);
        } finally {
            $client->setDebug(
                $request->getHeaders(),
                $request->getBody(),
                isset($response) ? $response->getStatusCode() : null,
                isset($response) ? $response->getHeaders() : null,
                isset($e) ? $e : null
            );

            $request->getBody()->rewind();
        }

        //$client->setSideload(null);

        return json_decode((string) $response->getBody());
    }
}
