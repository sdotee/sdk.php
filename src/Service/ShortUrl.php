<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: ShortUrl.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:53:03
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:23:02
 *
 **/

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
     * - custom_slug (string)
     * - title (string)
     * - password (string)
     * - expire_at (int64)
     * - expiration_redirect_url (string)
     * - tag_ids (array<int64>)
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
