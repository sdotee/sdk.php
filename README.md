# S.EE PHP SDK

Official PHP SDK for the [S.EE](https://s.ee) API. It supports short URLs, text sharing, files (including TUS large-file uploads), bio pages, QR codes, tags, usage, and token validation.

## Requirements

- PHP >= 8.1
- Guzzle >= 7.0

## Installation

Install via Composer:

```bash
composer require sdotee/sdk
```

or visit: https://packagist.org/packages/sdotee/sdk for more details.

## Usage

### Initialization

```php
use See\Client;

$apiKey = 'YOUR_API_KEY';
$client = new Client($apiKey);
```

### Short URL

**Create a Short URL:**

```php
try {
    $result = $client->shortUrl->create('https://example.com/long/url', 's.ee', [
        'custom_slug' => 'myshort',
        'title' => 'My Link'
    ]);

    echo "Short URL: " . $result['short_url'];
} catch (\See\Exception\SeeException $e) {
    echo "Error: " . $e->getMessage();
}
```

**Update a Short URL:**

```php
$client->shortUrl->update('s.ee', 'myshort', 'https://new-url.com', 'New Title');
```

**Delete a Short URL:**

```php
$client->shortUrl->delete('s.ee', 'myshort');
```

**History and statistics:**

```php
$links = $client->shortUrl->getHistory(page: 1);
$stats = $client->shortUrl->getVisitStatistics('s.ee', 'myshort', 'monthly');
$domains = $client->shortUrl->getDomains();

// Query-string integration. JSON mode is enabled by default by the SDK.
$link = $client->shortUrl->createSimple($apiKey, 'https://example.com', [
    'custom_slug' => 'example',
]);
```

### Text Paste

**Create Text Paste:**

```php
$result = $client->text->create('Hello World!', [
    'text_type' => 'markdown',
    'title' => 'My Note'
]);
echo $result['short_url'];
```

**Update Text Paste:**

```php
$client->text->update('s.ee', 'myslug', 'New Content', 'New Title');
```

**Delete Text Paste:**

```php
$client->text->delete('s.ee', 'myslug');
```

```php
$texts = $client->text->getHistory(page: 1);
$domains = $client->text->getDomains();
```

### File Service

**Upload File:**

```php
// Upload from path
$result = $client->file->upload('/path/to/image.png', 'image.png', [
    'domain' => 'fs.to',
    'custom_slug' => 'image',
]);
echo $result['url'];
echo $result['delete'];
```

**Delete File:**

```php
$client->file->delete($deleteKey);
```

**History and private files:**

```php
$files = $client->file->getHistory(page: 1);
$domains = $client->file->getDomains();
$download = $client->file->getPrivateDownloadUrl($fileId);
```

**Large files (TUS 1.0):**

```php
$path = '/path/to/archive.zip';
$upload = $client->file->createLargeUpload(basename($path), filesize($path), [
    'file_hash' => hash_file('sha256', $path),
    'is_private' => 1,
]);

if (!$upload['fast_upload']) {
    $handle = fopen($path, 'rb');
    $offset = $client->file->getLargeUploadOffset($upload['upload_id']);
    fseek($handle, $offset);

    while (!feof($handle)) {
        $chunk = fread($handle, 8 * 1024 * 1024);
        $offset = $client->file->uploadChunk($upload['upload_id'], $chunk, $offset);
    }

    fclose($handle);
    $file = $client->file->completeLargeUpload($upload['upload_id']);
}
```

Use `getLargeUploadProgress()` to query server-side progress, `cancelLargeUpload()` to cancel the session through the JSON API, or `terminateLargeUpload()` to terminate it through TUS.

### Bio Pages

```php
$bio = $client->bioPage->create('My profile', [
    'description' => 'About me',
    'domain' => 's.ee',
    'custom_links' => [
        ['title' => 'Website', 'url' => 'https://example.com'],
    ],
]);

$client->bioPage->update($bio['bio_page_id'], 'Updated profile');
$pages = $client->bioPage->getHistory(page: 1);
$client->bioPage->delete($bio['bio_page_id']);
```

### QR Codes

```php
$qrCode = $client->qrCode->create('https://example.com', 'Example', [
    'domain' => 's.ee',
    'custom_slug' => 'example-qr',
]);

$codes = $client->qrCode->getHistory(page: 1);
$client->qrCode->delete('s.ee', $qrCode['slug']);
```

### Common

**Get Available Domains:**

```php
$domains = $client->common->getDomains();
print_r($domains);
```

**Get Tags:**

```php
$tags = $client->common->getTags();
print_r($tags);
```

**Get account usage:**

```php
$usage = $client->common->getUsage();
```

### Token Validation

This endpoint validates a token supplied in the request body and does not rely on the client's authorization header.

```php
$token = $client->token->check('TOKEN_TO_VALIDATE');
var_dump($token['valid']);
```

## Responses and Errors

Successful methods return the API response's `data` value. Endpoints without a `data` value return the complete response object, and successful empty TUS responses return an empty array. API, HTTP, transport, and invalid JSON failures throw `See\Exception\SeeException`.

## Examples

There are example scripts in the `examples/` directory demonstrating how to use different services.

To run the examples, you need to set the `SEE_API_KEY` environment variable. Optionally, you can set `SEE_API_BASE` if you need to use a different API endpoint.

**Short URL Example:**

```bash
export SEE_API_KEY="your_api_key"
php examples/url.php
```

**Text Paste Example:**

```bash
export SEE_API_KEY="your_api_key"
php examples/text.php
```

**File Service Example:**

```bash
export SEE_API_KEY="your_api_key"
php examples/file.php
```

## Testing

The project includes request-level PHPUnit tests for the service modules and shared response handling.

Run all unit tests:

```bash
./vendor/bin/phpunit
```

Run tests for a specific module:

```bash
# Test Short URL service
./vendor/bin/phpunit tests/ShortUrlTest.php

# Test Text service
./vendor/bin/phpunit tests/TextTest.php

# Test File service
./vendor/bin/phpunit tests/FileTest.php

# Test the extended API surface
./vendor/bin/phpunit tests/ApiCoverageTest.php
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
