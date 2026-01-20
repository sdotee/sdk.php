<?php

namespace See\Service;

use See\Exception\SeeException;

class Common extends AbstractService
{
    /**
     * Get available domains for short links or text pastes.
     *
     * @return array
     * @throws SeeException
     */
    public function getDomains(): array
    {
        $data = $this->request('GET', 'domains');
        return $data['domains'] ?? [];
    }

    /**
     * Get available tags.
     *
     * @return array
     * @throws SeeException
     */
    public function getTags(): array
    {
        $data = $this->request('GET', 'tags');
        return $data['tags'] ?? [];
    }
}
