<?php

declare(strict_types=1);

namespace NotilifyAPI;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class Request {

    public const API_URL = 'https://api.notilify.com/v1';

    protected ClientInterface $client;

    protected array $lastResponse = [];

    /**
     * Constructor
     * Set client.
     *
     * @param ClientInterface $client Optional. Client to set.
     */
    public function __construct(ClientInterface $client = null) {
        $this->client = $client ?? new Client(['handler' => GuzzleFactory::handler()]);
    }

    /**
     * Get the latest full response from the Notilify API.
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function getLastResponse(): array {
        return $this->lastResponse;
    }

    /**
     * Handle response errors.
     *
     * @param string $body The raw, unparsed response body.
     * @param int $status The HTTP status code, passed along to any exceptions thrown.
     *
     * @throws NotilifyAPIException
     *
     * @return void
     */
    protected function handleResponseError(string $body, int $status): void {
        $parsedBody = json_decode($body);
        $error = $parsedBody->error ?? null;

        if ($error) {
            // It's an API call error
            throw  new NotilifyAPIException($error, $status);
        } else {
            // Something went really wrong, we don't know what
            throw new NotilifyAPIException('An unknown error occurred.', $status);
        }
    }

    /**
     * Make a request to the "api" endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $options
     *
     * @throws NotilifyAPIException
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function api(string $method, string $uri, array $options): array {
        return $this->send($method, self::API_URL . $uri, $options);
    }

    /**
     * Make a request to Notilify.
     * You'll probably want to use one of the convenience methods instead.
     *
     * @param string $method The HTTP method to use.
     * @param string $url The URL to request.
     * @param array $options
     *
     * @throws NotilifyAPIException
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    public function send(string $method, string $url, array $options): array {
        // Reset any old responses
        $this->lastResponse = [];

        try {
            $response = $this->client->request($method, $url, $options);
        } catch (ClientException $exception) {
            $this->handleResponseError($exception->getResponse()->getBody()->getContents(), $exception->getResponse()->getStatusCode());
        }

        $body = $parsedBody = $response->getBody();
        $status = $response->getStatusCode();
        $parsedHeaders = $response->getHeaders();

        if (in_array('application/json', $response->getHeader('Content-Type'))) {
            $parsedBody = json_decode($body->getContents(), true);
        }

        $this->lastResponse = [
            'body' => $parsedBody,
            'headers' => $parsedHeaders,
            'status' => $status,
            'url' => $url,
        ];

        return $this->lastResponse;
    }
}
