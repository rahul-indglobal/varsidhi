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

namespace Webkul\StorePickup\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Webkul\StorePickup\Api\Data\StoresInterface;

class Stores extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /**
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Webkul\StorePickup\Helper\Data $dataHelper
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        $connectionName = null
    ) {
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $connectionName);
    }

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webkul_storepickup_stores', 'entity_id');
    }

    /**
     * Get store details
     * @param int $storeId
     * @return array
     */
    public function getStoreDetails($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_details');
        $select = $connection->select()
            ->from(['details' => $this->getTable($tableName)], ['*'])
            ->where('details.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $row = $connection->fetchRow($select);
            $connection->commit();
            return $row;
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * Get store timings
     * @param int $storeId
     * @return array
     */
    public function getStoreTimings($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_timings');
        $select = $connection->select()
            ->from(['timings' => $this->getTable($tableName)], ['*'])
            ->where('timings.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $row = $connection->fetchRow($select);
            $connection->commit();
            if (!empty($row['timings'])) {
                $row['timings'] = $this->dataHelper->unserialize($row['timings']);
            }

            return $row;
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * Get store holidays
     * @param int $storeId
     * @return array
     */
    public function getStoreHolidays($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_stores_holidays_relation');
        $select = $connection->select()
            ->from(['holidays' => $this->getTable($tableName)], ['holiday_id'])
            ->where('holidays.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $rows = $connection->fetchAll($select);
            $connection->commit();
            if (!empty($row['holidays'])) {
                $row['holidays'] = $this->dataHelper->unserialize($row['holidays']);
            }

            if (!empty($rows)) {
                $temp = [];
                $temp['holidays'] = array_column($rows, 'holiday_id');
                return $temp;
            }

            return '';
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * is store details available
     * @param int $storeId
     * @return boolean
     */
    public function isStoreDetailsAvailable($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_details');
        $select = $connection->select()
            ->from(['details' => $this->getTable($tableName)], ['entity_id'])
            ->where('details.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $row = $connection->fetchRow($select);
            $connection->commit();
            if (!empty($row)) {
                return true;
            }

            return false;
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * is store timings available
     * @param int $storeId
     * @return boolean
     */
    public function isStoreTimingsAvailable($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_timings');
        $select = $connection->select()
            ->from(['timings' => $this->getTable($tableName)], ['entity_id'])
            ->where('timings.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $row = $connection->fetchRow($select);
            $connection->commit();
            if (!empty($row)) {
                return true;
            }

            return false;
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * is store holidays available
     * @param int $storeId
     * @return boolean
     */
    public function isStoreHolidaysAvailable($storeId)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName('webkul_storepickup_stores_holidays_relation');
        $select = $connection->select()
            ->from(['holidays' => $this->getTable($tableName)], ['entity_id'])
            ->where('holidays.store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $row = $connection->fetchRow($select);
            $connection->commit();
            if (!empty($row)) {
                return true;
            }

            return false;
        } catch (\Exception $ex) {
            $connection->rollBack();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->delete($object);
    }
}
