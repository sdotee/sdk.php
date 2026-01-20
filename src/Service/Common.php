<?php

/**
 * Copyright (c) 2026 S.EE Development Team
 *
 * This source code is licensed under the MIT License,
 * which is located in the LICENSE file in the source tree's root directory.
 *
 * File: Common.php
 * Author: S.EE Development Team <dev@s.ee>
 * File Created: 2026-01-20 17:53:38
 *
 * Modified By: S.EE Development Team <dev@s.ee>
 * Last Modified: 2026-01-20 18:22:48
 *
 **/


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
