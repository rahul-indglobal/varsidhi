<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\StorePickup\Model\ResourceModel\Stores\Relation;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Webkul\StorePickup\Api\Data\StoresInterface;
use Webkul\StorePickup\Model\ResourceModel\Stores;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Webkul\StorePickup\Model\ResourceModel\Stores
     */
    protected $resourceStores;

    /**
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Webkul\StorePickup\Model\ResourceModel\Stores $resourceStores
     */
    public function __construct(
        MetadataPool $metadataPool,
        Stores $resourceStores
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceStores = $resourceStores;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        $id = (int)$entity->getEntityId();
        if ($id) {
            $data = $this->resourceStores->getStoreDetails($id);
            $entity->setData('stores_details', $data);
            $data = $this->resourceStores->getStoreTimings($id);
            $entity->setData('stores_timings', $data);
            $data = $this->resourceStores->getStoreHolidays($id);
            $entity->setData('stores_holidays', $data);
        }

        return $entity;
    }
}
