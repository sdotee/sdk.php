<?php

namespace See\Service;

class BioPage extends AbstractService
{
  public function create(string $title, array $options = []): array
  {
    return $this->request('POST', 'bio', [
      'json' => array_merge(['title' => $title], $options),
    ]);
  }

  public function update(int $id, string $title, array $options = []): mixed
  {
    return $this->request('PUT', 'bio', [
      'json' => array_merge(['id' => $id, 'title' => $title], $options),
    ]);
  }

  public function delete(int $id): mixed
  {
    return $this->request('DELETE', 'bio', ['json' => ['id' => $id]]);
  }

  public function getHistory(int $page = 1): array
  {
    return $this->request('GET', 'bios', ['query' => ['page' => $page]]);
  }
}
