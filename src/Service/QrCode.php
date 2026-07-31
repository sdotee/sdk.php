<?php

namespace See\Service;

class QrCode extends AbstractService
{
  public function create(string $targetUrl, string $title, array $options = []): array
  {
    return $this->request('POST', 'qrcode', [
      'json' => array_merge([
        'target_url' => $targetUrl,
        'title' => $title,
      ], $options),
    ]);
  }

  public function delete(string $domain, string $slug): mixed
  {
    return $this->request('DELETE', 'qrcode', [
      'json' => compact('domain', 'slug'),
    ]);
  }

  public function getHistory(int $page = 1): array
  {
    return $this->request('GET', 'qrcodes', ['query' => ['page' => $page]]);
  }
}
