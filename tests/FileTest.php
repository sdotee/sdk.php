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

    public function testFileUploadUsesDocumentedMultipartFields(): void
    {
        $history = [];
        $client = $this->createMockClient([
            new Response(200, [], json_encode(['code' => 200, 'data' => []]))
        ], $history);

        $client->file->upload('content', 'test.txt', [
            'domain' => 'fs.to',
            'custom_slug' => 'notes',
        ]);

        $request = $history[0]['request'];
        $body = (string) $request->getBody();
        $this->assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('name="file"; filename="test.txt"', $body);
        $this->assertStringContainsString('name="domain"', $body);
        $this->assertStringContainsString('name="custom_slug"', $body);
        $this->assertStringNotContainsString('name="filename"', $body);
    }

    public function testTusUploadWorkflow(): void
    {
        $history = [];
        $client = $this->createMockClient([
            new Response(204, ['Upload-Offset' => '5']),
            new Response(204, ['Upload-Offset' => '5']),
            new Response(204),
        ], $history);

        $this->assertSame(5, $client->file->uploadChunk('upload/id', 'hello', 0));
        $this->assertSame(5, $client->file->getLargeUploadOffset('upload/id'));
        $client->file->terminateLargeUpload('upload/id');

        $this->assertSame('PATCH', $history[0]['request']->getMethod());
        $this->assertSame('file/large-file-tus/upload%2Fid', $history[0]['request']->getUri()->getPath());
        $this->assertSame('1.0.0', $history[0]['request']->getHeaderLine('Tus-Resumable'));
        $this->assertSame('0', $history[0]['request']->getHeaderLine('Upload-Offset'));
        $this->assertSame('HEAD', $history[1]['request']->getMethod());
        $this->assertSame('DELETE', $history[2]['request']->getMethod());
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
