<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: Client.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:52:27
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:18:24
 */

namespace See;

use GuzzleHttp\Client as GuzzleClient;
use See\Service\Common;
use See\Service\File;
use See\Service\ShortUrl;
use See\Service\Text;

class Client
{
    private const BASE_URI = 'https://s.ee/api/v1/';

    public Common $common;
    public ShortUrl $shortUrl;
    public Text $text;
    public File $file;

    private GuzzleClient $httpClient;

    public function __construct(string $apiKey, string $baseUri = self::BASE_URI, ?GuzzleClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new GuzzleClient([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ]);

        $this->common = new Common($this->httpClient);
        $this->shortUrl = new ShortUrl($this->httpClient);
        $this->text = new Text($this->httpClient);
        $this->file = new File($this->httpClient);
    }
}
