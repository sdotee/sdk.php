<?php

require __DIR__ . '/../vendor/autoload.php';

use See\Client;
use See\Exception\SeeException;

$apiKey = getenv('SEE_API_KEY');
if (!$apiKey) {
    die("Please set SEE_API_KEY environment variable.\n");
}
$baseUrl = getenv('SEE_API_BASE') ?: 'https://s.ee/api/v1/';

$client = new Client($apiKey, $baseUrl);

echo ">>> Starting URL Service Examples\n\n";

try {
    // 1. Get available domains
    echo "1. Fetching available domains...\n";
    $domains = $client->common->getDomains();
    if (empty($domains)) {
        die("No domains available.\n");
    }
    $domain = $domains[0];
    echo "Using domain: {$domain}\n\n";

    // 2. Create a Short URL
    $targetUrl = "https://www.example.com/?t=" . time();
    echo "2. Creating short URL for: {$targetUrl}\n";
    
    $result = $client->shortUrl->create($targetUrl, $domain, [
        'title' => 'Example Link via SDK'
    ]);
    
    $shortUrl = $result['short_url'];
    $slug = $result['slug']; // Save slug for cleanup
    
    echo "Created: {$shortUrl}\n";
    echo "Slug: {$slug}\n\n";

    // 3. Update the Short URL
    echo "3. Updating Short URL...\n";
    $newTarget = "https://www.example.com/?t=" . (time() + 60);
    $updateResult = $client->shortUrl->update($domain, $slug, $newTarget, 'Updated Title');
    echo "Updated target to: {$newTarget}\n\n";

    // 4. Delete the Short URL
    echo "4. Deleting Short URL...\n";
    $client->shortUrl->delete($domain, $slug);
    echo "Deleted successfully.\n";

} catch (SeeException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}

echo "\n>>> Finished URL Service Examples\n";
