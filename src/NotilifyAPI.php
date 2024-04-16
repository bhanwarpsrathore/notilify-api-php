<?php

declare(strict_types=1);

namespace NotilifyAPI;

class NotilifyAPI {

    protected string $apiKey = '';
    protected array $lastResponse = [];
    protected ?Request $request = null;

    /**
     * Constructor
     * Set options and class instances to use.
     *
     * @param Request $request Optional. The Request object to use.
     */
    public function __construct(?Request $request = null) {
        $this->request = $request ?? new Request();
    }

    /**
     * Set the API key
     * 
     * @param string $apiKey The API key.
     * @return self
     */
    public function setApiKey(string $apiKey): self {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Add authorization headers.
     *
     * @param $headers array. Optional. Additional headers to merge with the authorization headers.
     *
     * @return array Authorization headers, optionally merged with the passed ones.
     */
    protected function authHeaders(array $headers = []): array {
        if ($this->apiKey) {
            $headers = array_merge($headers, [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]);
        }

        return $headers;
    }

    /**
     * Get the latest full response from the Dropbox API.
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
     * Send a request to the Notilify API
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param string|array $parameters Optional. Query string parameters or HTTP body, depending on $method.
     *
     * @throws NotilifyAPIException
     *
     * @return array Response data.
     * - array body The response body.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     */
    protected function apiRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = []
    ): array {
        $headers = $this->authHeaders($headers);

        $options = ['headers' => $headers];
        if ($parameters) {
            $options['json'] = $parameters;
        }

        return $this->request->api($method, $uri, $options);
    }

    /**
     * Send single message
     * 
     * @param string $phoneNumber
     * @param string $senderId
     * @param string $message
     * @return array
     */
    public function message(string $phoneNumber, string $senderId, string $message): array {
        $uri = '/message';

        $parameters = [
            'phoneNumber' => $phoneNumber,
            'senderId' => $senderId,
            'message' => $message
        ];

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }
}
