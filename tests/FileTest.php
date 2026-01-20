<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: FileTest.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 18:14:08
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:23:51
 *
 **/


namespace See\Tests;

use GuzzleHttp\Psr7\Response;

class FileTest extends BaseTestCase
{
    /**
     * Test file upload functionality.
     */
    public function testFileUpload()
    {
        $mockBody = json_encode([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'url' => 'https://s.ee/image.png',
                'filename' => 'test.png',
                'size' => 1024
            ]
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        // Mocking file upload is strictly about seeing if request is sent,
        // but here we just check if response is handled.
        // pass a string content to avoid file read issues in test
        $result = $client->file->upload('dummy content', 'test.png');

        $this->assertEquals('test.png', $result['filename']);
    }

    /**
     * Test file deletion functionality.
     */
    public function testFileDelete()
    {
        $mockBody = json_encode([
            'code' => "200",
            'message' => 'success',
            'success' => true
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody)
        ]);

        $result = $client->file->delete('del_key_123');

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }
}
