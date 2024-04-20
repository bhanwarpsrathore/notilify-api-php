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
     * @param string $senderId
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function message(string $senderId, string $phoneNumber, string $message): array {
        $uri = '/message';

        $parameters = [
            'senderId' => $senderId,
            'phoneNumber' => $phoneNumber,
            'message' => $message
        ];

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }

    /**
     * Send bulk messages
     * 
     * @param string $senderId
     * @param array $data
     * @return array
     */
    public function bulkMessages(string $senderId, array $data): array {
        $uri = '/message/bulk';

        $parameters = [
            'senderId' => $senderId,
            'data' => $data
        ];

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }

    /**
     * Get messages
     * 
     * @param int $pageNumber
     * @param int $perPage
     */
    public function getMessages(int $pageNumber = 1, int $perPage = 40): array {
        $uri = '/message?perPage=' . $perPage . '&pageNumber=' . $pageNumber;

        $this->lastResponse = $this->apiRequest('GET', $uri);

        return $this->lastResponse['body'];
    }

    /**
     * Find message by id
     * 
     * @param string $id
     * @return array
     */
    public function findMessage(string $id): array {
        $uri = '/message?id=' . $id;

        $this->lastResponse = $this->apiRequest('GET', $uri);

        return $this->lastResponse['body'];
    }

    /**
     * Create OTP
     * 
     * @param string $phoneNumber The phone number to be verified. It should be in E.164 format (e.g., +1234567890).
     * @param string $message The message to be sent to the user eg; "Here is your OTP to use to complete your purchase"
     * @param string $senderId Optional. The senderId will default to a phone number if none is selected.
     * @param int $length Optional. The length of the otp to be sent to the user, the default is 6.
     * @param int $expiryTime Optional. The expiry time of the OTP in minutes. (default is "20 minutes").
     * @param string $type Optional. The allowed values are ('number', 'alphanumeric', 'letter'), default is alphanumeric
     * @return array
     */
    public function createOtp(string $phoneNumber, string $message, string $senderId = '', int $length = 6, int $expiryTime = 20, string $type = 'alphanumeric'): array {
        $uri = '/otp';

        $parameters = [
            'phoneNumber' => $phoneNumber,
            'message' => $message,
            'length' => (string) $length,
            'expiryTime' => (string) $expiryTime,
            'type' => $type
        ];

        if (!empty($senderId)) {
            $parameters['senderId'] = $senderId;
        }

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }

    /**
     * Verify OTP
     * 
     * @param string $phoneNumber The phone number to be verified. It should be in E.164 format (e.g., +1234567890).
     * @param string $otp The OTP to be verified.
     * @return array
     */
    public function verifyOtp(string $phoneNumber, string $otp): array {
        $uri = '/otp/verify';

        $parameters = [
            'phoneNumber' => $phoneNumber,
            'otp' => $otp
        ];

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }

    /**
     * Create Sender Id
     * 
     * @param string $senderId
     * @return array
     */
    public function createSenderId(string $senderId): array {
        $uri = '/sender-id';

        $parameters = [
            'senderId' => $senderId
        ];

        $this->lastResponse = $this->apiRequest('POST', $uri, $parameters);

        return $this->lastResponse['body'];
    }

    /**
     * Get Sender Ids
     * 
     * @return array
     */
    public function getSenderIds(): array {
        $uri = '/sender-id';

        $this->lastResponse = $this->apiRequest('GET', $uri);

        return $this->lastResponse['body'];
    }
}
