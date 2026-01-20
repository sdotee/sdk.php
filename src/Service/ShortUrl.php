<?php

namespace See\Service;

use See\Exception\SeeException;

class ShortUrl extends AbstractService
{
    /**
     * Create a new short URL.
     *
     * @param string $targetUrl
     * @param string $domain
     * @param array $options Optional parameters:
     *  - custom_slug (string)
     *  - title (string)
     *  - password (string)
     *  - expire_at (int64)
     *  - expiration_redirect_url (string)
     *  - tag_ids (array<int64>)
     * @return array
     * @throws SeeException
     */
    public function create(string $targetUrl, string $domain, array $options = []): array
    {
        $payload = array_merge([
            'target_url' => $targetUrl,
            'domain' => $domain,
        ], $options);

        // Filter out null values to avoid sending them? API might accept nulls, but usually optional fields are omitted.
        // Let's rely on array_merge and user passing needed options.
        
        return $this->request('POST', 'shorten', [
            'json' => $payload,
        ]);
    }

    /**
     * Update an existing short URL.
     *
     * @param string $domain
     * @param string $slug
     * @param string $targetUrl
     * @param string $title
     * @return mixed
     * @throws SeeException
     */
    public function update(string $domain, string $slug, string $targetUrl, string $title): mixed
    {
        return $this->request('PUT', 'shorten', [
            'json' => [
                'domain' => $domain,
                'slug' => $slug,
                'target_url' => $targetUrl,
                'title' => $title,
            ],
        ]);
    }

    /**
     * Delete a short URL.
     *
     * @param string $domain
     * @param string $slug
     * @return mixed
     * @throws SeeException
     */
    public function delete(string $domain, string $slug): mixed
    {
        // DELETE request usually does not have body in many specs, but here the docs say "请求参数 (JSON)".
        // Guzzle supports body in DELETE.
        return $this->request('DELETE', 'shorten', [
            'json' => [
                'domain' => $domain,
                'slug' => $slug,
            ],
        ]);
    }
}
