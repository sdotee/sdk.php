<?php

namespace See\Tests;

use GuzzleHttp\Psr7\Response;

class ApiCoverageTest extends BaseTestCase
{
  public function testBioQrCodeAndTokenRequests(): void
  {
    $history = [];
    $client = $this->createMockClient([
      $this->successResponse(['bio_page_id' => 1]),
      $this->successResponse([]),
      $this->successResponse([]),
      $this->successResponse(['short_url' => 'https://s.ee/qr']),
      $this->successResponse([]),
      $this->successResponse(['valid' => true]),
    ], $history);

    $client->bioPage->create('Profile', ['domain' => 's.ee']);
    $client->bioPage->update(1, 'Updated');
    $client->bioPage->delete(1);
    $client->qrCode->create('https://example.com', 'Example');
    $client->qrCode->delete('s.ee', 'qr');
    $result = $client->token->check('token-value');

    $this->assertTrue($result['valid']);
    $this->assertRequest($history[0], 'POST', 'bio', ['title' => 'Profile', 'domain' => 's.ee']);
    $this->assertRequest($history[1], 'PUT', 'bio', ['id' => 1, 'title' => 'Updated']);
    $this->assertRequest($history[2], 'DELETE', 'bio', ['id' => 1]);
    $this->assertRequest($history[3], 'POST', 'qrcode', [
      'target_url' => 'https://example.com',
      'title' => 'Example',
    ]);
    $this->assertRequest($history[4], 'DELETE', 'qrcode', ['domain' => 's.ee', 'slug' => 'qr']);
    $this->assertRequest($history[5], 'POST', 'token/check', ['token' => 'token-value']);
  }

  public function testHistoryStatisticsAndLargeFileRequests(): void
  {
    $history = [];
    $client = $this->createMockClient(array_fill(0, 8, $this->successResponse([])), $history);

    $client->shortUrl->getHistory(2);
    $client->shortUrl->getVisitStatistics('s.ee', 'abc', 'monthly');
    $client->text->getHistory(3);
    $client->file->getHistory(4);
    $client->file->getPrivateDownloadUrl(42);
    $client->file->createLargeUpload('archive.zip', 1024, ['is_private' => 1]);
    $client->file->getLargeUploadProgress('upload-1');
    $client->file->completeLargeUpload('upload-1');

    $this->assertSame('page=2', $history[0]['request']->getUri()->getQuery());
    $this->assertSame('domain=s.ee&slug=abc&period=monthly', $history[1]['request']->getUri()->getQuery());
    $this->assertSame('texts', $history[2]['request']->getUri()->getPath());
    $this->assertSame('files', $history[3]['request']->getUri()->getPath());
    $this->assertSame('file_id=42', $history[4]['request']->getUri()->getQuery());
    $this->assertRequest($history[5], 'POST', 'file/large-file/create', [
      'file_name' => 'archive.zip',
      'file_size' => 1024,
      'is_private' => 1,
    ]);
    $this->assertSame('upload_id=upload-1', $history[6]['request']->getUri()->getQuery());
    $this->assertRequest($history[7], 'POST', 'file/large-file/complete', ['upload_id' => 'upload-1']);
  }

  private function successResponse(array $data): Response
  {
    return new Response(200, [], json_encode(['code' => 200, 'data' => $data]));
  }

  private function assertRequest(array $transaction, string $method, string $path, array $body): void
  {
    $request = $transaction['request'];
    $this->assertSame($method, $request->getMethod());
    $this->assertSame($path, $request->getUri()->getPath());
    $this->assertSame($body, json_decode((string) $request->getBody(), true));
  }
}
