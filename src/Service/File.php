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
    public function upload($fileContent, string $filename): array
    {
        $options = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => (is_string($fileContent) && file_exists($fileContent))
                        ? fopen($fileContent, 'r')
                        : $fileContent,
                    'filename' => $filename,
                ],
                [
                    'name'     => 'filename',
                    'contents' => $filename
                ]
            ],
        ];

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
}
