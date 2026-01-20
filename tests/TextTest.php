<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: TextTest.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 18:14:01
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:24:17
 *
 **/


namespace See\Tests;

use GuzzleHttp\Psr7\Response;

class TextTest extends BaseTestCase
{
    /**
     * Test creating a text Short URL.
     */
    public function testTextCreate()
    {
        $mockBody = json_encode([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'short_url' => 'https://s.ee/txt',
                'slug' => 'txt'
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        $result = $client->text->create('Hello World');

        $this->assertEquals('https://s.ee/txt', $result['short_url']);
    }
}
