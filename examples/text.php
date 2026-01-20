<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: text.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:58:55
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:21:34
 *
 **/

require __DIR__ . '/../vendor/autoload.php';

use See\Client;
use See\Exception\SeeException;

$apiKey = getenv('SEE_API_KEY');
if (!$apiKey) {
    die("Please set SEE_API_KEY environment variable.\n");
}
$baseUrl = getenv('SEE_API_BASE') ?: 'https://s.ee/api/v1/';

$client = new Client($apiKey, $baseUrl);

echo ">>> Starting Text Paste Service Examples\n\n";

try {
    // 1. Create a Text Paste
    echo "1. Creating text paste...\n";
    $content = "This is a test paste created via the PHP SDK at " . date('Y-m-d H:i:s');

    // Attempt to get a domain specifically for text if needed, but text endpoint accepts domain optional or default?
    // SDK create method signature: create(string $content, array $options = [])
    // Let's check domains first if we want to be explicit, but the API might pick default.
    // Let's pass a domain if available just in case.
    $domains = $client->common->getDomains();
    foreach ($domains as $d) {
        print($d . "\n");
    }

    $domain = "z.ee"; // Example domain, change as needed or leave null for default.
    $options = [
        'title' => 'SDK Text Example',
        'text_type' => 'plain_text'
    ];
    if ($domain) {
        $options['domain'] = $domain;
    }

    $result = $client->text->create($content, $options);

    $shortUrl = $result['short_url'];
    $slug = $result['slug'];
    // Recover domain used from result if not returned? Result has slug, custom_slug, short_url.
    // We should probably know the domain from `short_url` or the one we passed.
    // To update/delete we need domain and slug.
    // If we didn't pass domain, we might need to parse it from short_url or assume default.
    // To be safe for the example, let's assume the domain we fetched is used.

    // Parse domain from short_url for update/delete if we didn't force one?
    // Ex: https://s.ee/slug -> domain is s.ee
    $parsedUrl = parse_url($shortUrl);
    $usedDomain = $parsedUrl['host'];

    echo "Created: {$shortUrl}\n";
    echo "Slug: {$slug}\n";
    echo "Domain: {$usedDomain}\n\n";

    // 2. Update the Text Paste
    echo "2. Updating text paste...\n";
    $newContent = $content . "\n\nUpdated content.";
    $client->text->update($usedDomain, $slug, $newContent, 'Updated Text Title');
    echo "Updated content successfully.\n\n";

    // 3. Delete the Text Paste
    echo "3. Deleting text paste...\n";
    $client->text->delete($usedDomain, $slug);
    echo "Deleted successfully.\n";
} catch (SeeException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}

echo "\n>>> Finished Text Paste Service Examples\n";
