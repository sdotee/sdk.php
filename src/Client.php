<?php

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
