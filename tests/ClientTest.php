<?php

namespace See\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use See\Client;
use See\Exception\SeeException;

class ClientTest extends TestCase
{
    private function createMockClient(array $queue): Client
    {
        $mock = new MockHandler($queue);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new GuzzleClient(['handler' => $handlerStack]);

        return new Client('fake_key', 'https://api.test', $httpClient);
    }

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

    public function testExceptionHandling()
    {
        $mockBody = json_encode([
            'code' => 401,
            'message' => 'Unauthorized',
            'data' => null
        ]);

        $client = $this->createMockClient([
            new Response(200, [], $mockBody) // API returns 200 HTTP usually but checks body code
        ]);

        $this->expectException(SeeException::class);
        $this->expectExceptionMessage('Unauthorized');
        $this->expectExceptionCode(401);

        $client->common->getTags();
    }
}
