<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: BaseTestCase.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 18:13:49
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:21:14
 *
 **/

namespace See\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use See\Client;

class BaseTestCase extends TestCase
{
    /**
     * Create a Client instance with a mocked HTTP client.
     *
     * @param array $queue Request/Response queue for the mock handler
     * @return Client
     */
    protected function createMockClient(array $queue): Client
    {
        $mock = new MockHandler($queue);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new GuzzleClient(['handler' => $handlerStack]);

        return new Client('fake_key', 'https://api.test', $httpClient);
    }
}
