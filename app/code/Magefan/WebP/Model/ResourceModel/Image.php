<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\WebP\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Image extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magefan_webp_images', 'image_id');
    }
}
