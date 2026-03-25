<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\WebP\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'image_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magefan\WebP\Model\Image::class, \Magefan\WebP\Model\ResourceModel\Image::class);
    }
}
