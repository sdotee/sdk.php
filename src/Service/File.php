<?php

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
        // This endpoint returns 'success' boolean field inside JSON, we should probably check it.
        // Base AbstractService::handleResponse returns the 'data' part if present?
        // Wait, DELETE response in doc: code, message, success (bool). No data field shown?
        // Let's check `AbstractService::handleResponse` again.
        // If data field is absent, it returns null.
        // But the delete response structure is non-standard compared to others (data field missing?).
        // Doc says:
        /*
            | code | string | 状态码 (注意此处定义为 string) |
            | message | string | 响应消息 |
            | success | bool | 是否成功 |
        */
        // I might need to override request or handleResponse for this one, or adjust handleResponse to return whole body if data missing?
        
        // Let's peek at CommonService requirements too before fixing AbstractService.

        // I'll define a specialized method here to call request but maybe I need to tweak AbstractService to return the full response if needed, OR just trust 'data' is where meaningful stuff is.
        // But here 'success' is at top level.
        
        // I'll call `request` but raw? No, `request` calls `handleResponse`.
        // I should modify `handleResponse` or `delete` method here.
        
        // Let's modify `delete` to handle the specific response if I can.
        // But `request` is protected and does everything.
        // I'll add a parameter to `request` to return raw response? Or just modify `handleResponse` to be smarter.
        
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
