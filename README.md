# S.EE PHP SDK

Official PHP SDK for [S.EE](https://s.ee) API, providing easy access to URL shortening, text pasting, and file services.

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

### File Service

**Upload File:**

```php
// Upload from path
$result = $client->file->upload('/path/to/image.png', 'image.png');
echo $result['url'];
echo $result['delete']; // Delete key
```

**Delete File:**

```php
$client->file->delete($deleteKey);
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

The project includes PHPUnit tests for each service module (`ShortUrl`, `Text`, `File`).

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
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
