<?php

namespace See\Service;

class Token extends AbstractService
{
  public function check(string $token): array
  {
    return $this->request('POST', 'token/check', [
      'json' => ['token' => $token],
    ]);
  }
}
