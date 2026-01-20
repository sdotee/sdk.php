<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: AbstractService.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:52:21
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:22:42
 *
 **/

namespace See\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use See\Exception\SeeException;

abstract class AbstractService
{
    public function __construct(protected ClientInterface $httpClient) {}

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

        if (!is_array($data)) {
            throw new SeeException('Invalid response data format: ' . $body);
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
