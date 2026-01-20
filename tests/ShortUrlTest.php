<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: ShortUrlTest.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 18:13:55
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:24:10
 *
 **/

namespace See\Tests;

use GuzzleHttp\Psr7\Response;

class ShortUrlTest extends BaseTestCase
{
    /**
     * Test creating a short URL.
     */
    public function testShortenCreate()
    {
        $mockBody = json_encode([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'short_url' => 'https://s.ee/abc',
                'slug' => 'abc',
                'custom_slug' => ''
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        $result = $client->shortUrl->create('https://google.com', 's.ee');

        $this->assertIsArray($result);
        $this->assertEquals('abc', $result['slug']);
    }
}
