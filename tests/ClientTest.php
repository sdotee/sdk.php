<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: ClientTest.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 18:14:55
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:23:45
 *
 **/

namespace See\Tests;

use GuzzleHttp\Psr7\Response;
use See\Exception\SeeException;

class ClientTest extends BaseTestCase
{
    /**
     * Test fetching available domains (Common service).
     */
    public function testCommonDomains()
    {
        $mockBody = json_encode([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'domains' => ['s.ee', 'example.com']
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        $domains = $client->common->getDomains();
        $this->assertCount(2, $domains);
        $this->assertEquals('s.ee', $domains[0]);
    }

    /**
     * Test exception handling for API errors.
     */
    public function testExceptionHandling()
    {
        $mockBody = json_encode([
            'code' => 401,
            'message' => 'Unauthorized',
            'data' => null
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        $this->expectException(SeeException::class);
        $this->expectExceptionMessage('Unauthorized');
        $this->expectExceptionCode(401);

        $client->common->getTags();
    }
}
