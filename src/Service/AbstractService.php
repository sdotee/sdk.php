<?php

namespace See\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use See\Exception\SeeException;

abstract class AbstractService
{
    public function __construct(protected ClientInterface $httpClient)
    {
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws SeeException
     */
    protected function handleResponse(ResponseInterface $response): mixed
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SeeException('Failed to decode JSON response: ' . $body);
        }

        $code = $data['code'] ?? null;
        
        // Normalize code to int if possible for check
        $numericCode = is_numeric($code) ? (int)$code : $code;

        if ($numericCode !== 200 && $numericCode !== 0) {
            $message = $data['message'] ?? 'Unknown error';
            throw new SeeException($message, (int)$numericCode);
        }

        return $data['data'] ?? $data;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws SeeException
     */
    protected function request(string $method, string $uri, array $options = []): mixed
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new SeeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
