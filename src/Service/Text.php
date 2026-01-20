<?php

namespace See\Service;

use See\Exception\SeeException;

class Text extends AbstractService
{
    /**
     * Create a new text paste.
     *
     * @param string $content
     * @param array $options Optional parameters:
     *  - domain (string)
     *  - custom_slug (string)
     *  - title (string)
     *  - text_type (string) e.g., "plain", "markdown"
     *  - password (string)
     *  - expire_at (int64)
     *  - tag_ids (array<int64>)
     * @return array
     * @throws SeeException
     */
    public function create(string $content, array $options = []): array
    {
        $payload = array_merge([
            'content' => $content,
        ], $options);

        return $this->request('POST', 'text', [
            'json' => $payload,
        ]);
    }

    /**
     * Update an existing text paste.
     *
     * @param string $domain
     * @param string $slug
     * @param string $content
     * @param string|null $title
     * @return mixed
     * @throws SeeException
     */
    public function update(string $domain, string $slug, string $content, ?string $title = null): mixed
    {
        $payload = [
            'domain' => $domain,
            'slug' => $slug,
            'content' => $content,
        ];
        
        if ($title !== null) {
            $payload['title'] = $title;
        }

        return $this->request('PUT', 'text', [
            'json' => $payload,
        ]);
    }

    /**
     * Delete a text paste.
     *
     * @param string $domain
     * @param string $slug
     * @return mixed
     * @throws SeeException
     */
    public function delete(string $domain, string $slug): mixed
    {
        return $this->request('DELETE', 'text', [
            'json' => [
                'domain' => $domain,
                'slug' => $slug,
            ],
        ]);
    }
}
