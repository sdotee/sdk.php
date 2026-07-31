<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: File.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:53:25
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:22:55
 *
 **/


namespace See\Service;

use See\Exception\SeeException;

class File extends AbstractService
{
    /**
     * Upload a file.
     *
     * @param string|resource $fileContent Path to file or open resource.
     * @param string $filename The name of the file to be sent.
     * @return array
     * @throws SeeException
     */
    public function upload($fileContent, string $filename, array $fields = []): array
    {
        if (!is_resource($fileContent) && !is_string($fileContent)) {
            throw new \InvalidArgumentException('File content must be a path, string content, or resource.');
        }

        $contents = $fileContent;
        if (is_string($fileContent) && is_file($fileContent)) {
            $contents = fopen($fileContent, 'rb');
        }

        $options = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $contents,
                    'filename' => $filename,
                ],
            ],
        ];

        foreach (['domain', 'custom_slug'] as $name) {
            if (array_key_exists($name, $fields) && $fields[$name] !== null) {
                $options['multipart'][] = [
                    'name' => $name,
                    'contents' => (string) $fields[$name],
                ];
            }
        }

        return $this->request('POST', 'file/upload', $options);
    }

    /**
     * Delete a file using the delete key.
     *
     * @param string $hashKey
     * @return array
     * @throws SeeException
     */
    public function delete(string $hashKey): array
    {
        return $this->request('GET', "file/delete/" . $hashKey);
    }

    /**
     * Get available file domains.
     *
     * @return array
     * @throws SeeException
     */
    public function getDomains(): array
    {
        $data = $this->request('GET', 'file/domains');
        return $data['domains'] ?? [];
    }

    public function getHistory(int $page = 1): array
    {
        return $this->request('GET', 'files', ['query' => ['page' => $page]]);
    }

    public function getPrivateDownloadUrl(int $fileId): array
    {
        return $this->request('GET', 'file/private/download-url', [
            'query' => ['file_id' => $fileId],
        ]);
    }

    public function createLargeUpload(string $fileName, int $fileSize, array $options = []): array
    {
        return $this->request('POST', 'file/large-file/create', [
            'json' => array_merge([
                'file_name' => $fileName,
                'file_size' => $fileSize,
            ], $options),
        ]);
    }

    public function uploadChunk(string $uploadId, $chunk, int $offset): int
    {
        if (!is_resource($chunk) && !is_string($chunk)) {
            throw new \InvalidArgumentException('Chunk must be string content or a resource.');
        }

        $response = $this->sendRequest('PATCH', $this->tusUri($uploadId), [
            'body' => $chunk,
            'headers' => [
                'Content-Type' => 'application/offset+octet-stream',
                'Tus-Resumable' => '1.0.0',
                'Upload-Offset' => $offset,
            ],
        ]);
        $this->handleResponse($response);

        return (int) $response->getHeaderLine('Upload-Offset');
    }

    public function getLargeUploadOffset(string $uploadId): int
    {
        $response = $this->sendRequest('HEAD', $this->tusUri($uploadId), [
            'headers' => ['Tus-Resumable' => '1.0.0'],
        ]);
        $this->handleResponse($response);

        return (int) $response->getHeaderLine('Upload-Offset');
    }

    public function terminateLargeUpload(string $uploadId): void
    {
        $response = $this->sendRequest('DELETE', $this->tusUri($uploadId), [
            'headers' => ['Tus-Resumable' => '1.0.0'],
        ]);
        $this->handleResponse($response);
    }

    public function getLargeUploadProgress(string $uploadId): array
    {
        return $this->request('GET', 'file/large-file/progress', [
            'query' => ['upload_id' => $uploadId],
        ]);
    }

    public function completeLargeUpload(string $uploadId): array
    {
        return $this->request('POST', 'file/large-file/complete', [
            'json' => ['upload_id' => $uploadId],
        ]);
    }

    public function cancelLargeUpload(string $uploadId): mixed
    {
        return $this->request('DELETE', 'file/large-file/cancel', [
            'json' => ['upload_id' => $uploadId],
        ]);
    }

    private function tusUri(string $uploadId): string
    {
        return 'file/large-file-tus/' . rawurlencode($uploadId);
    }
}
