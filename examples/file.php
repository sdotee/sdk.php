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

echo ">>> Starting File Service Examples\n\n";

// Create a temporary file to upload
$tmpFile = tempnam(sys_get_temp_dir(), 'see_sdk_test');
file_put_contents($tmpFile, "Hello S.EE! This is a test file. \n Timestamp is " . time());
$filename = 'hello.txt';

try {
    // 1. Get available file domains
    echo "1. Fetching file domains...\n";
    $domains = $client->file->getDomains();
    if (!empty($domains)) {
        echo "Available file domains: " . implode(', ', $domains) . "\n";
    }
    echo "\n";

    // 2. Upload File
    echo "2. Uploading file...\n";
    $result = $client->file->upload($tmpFile, $filename);
    
    $fileUrl = $result['url'];
    $hashKey = $result['hash'];
    
    echo "Uploaded: {$fileUrl}\n";
    echo "Delete Key: {$hashKey}\n\n";

    // 3. Delete File
    echo "3. Deleting file...\n";
    $deleteResult = $client->file->delete($hashKey);
    // Depending on what delete returns, it might be the raw response or data
    // The previous implementation was: return $this->request('GET', "file/delete/" . $deleteKey);
    // And handleResponse returns data['data'] ?? $data.
    
    // Check if success
    if (isset($deleteResult['success']) && $deleteResult['success']) {
         echo "Deleted successfully.\n";
    } else {
         echo "Delete response: " . json_encode($deleteResult) . "\n";
    }

} catch (SeeException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
} finally {
    if (file_exists($tmpFile)) {
        unlink($tmpFile);
    }
}

echo "\n>>> Finished File Service Examples\n";
