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

class SaveHandler implements ExtensionInterface
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
     * @var \Webkul\StorePickup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Webkul\StorePickup\Model\ResourceModel\Stores $resourceStores
     * @param \Webkul\StorePickup\Helper\Data $datahelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        MetadataPool $metadataPool,
        Stores $resourceStores,
        \Webkul\StorePickup\Helper\Data $dataHelper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceStores = $resourceStores;
        $this->dataHelper = $dataHelper;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(StoresInterface::class);
        $connection = $entityMetadata->getEntityConnection();

        $detailsData = $this->resourceStores->isStoreDetailsAvailable((int)$entity->getEntityId());

        $entityData = $entity->getData();
        if (!empty($entityData['stores_details']) && !empty($entityData['details_data']['region'])) {
            $details = $entity->getStoresDetails();
            $details['region'] = $entityData['details_data']['region'];
            $entity->setStoresDetails($details);
        }

        if (!$detailsData) {
            $this->insertDetails($connection, $entity);
        } else {
            $this->updateDetails($connection, $entity);
        }

        $timingsData = $this->resourceStores->isStoreTimingsAvailable((int)$entity->getEntityId());
        if (!$timingsData) {
            $this->insertTimings($connection, $entity);
        } else {
            $this->updateTimings($connection, $entity);
        }

        $holidaysData = $this->resourceStores->isStoreHolidaysAvailable((int)$entity->getEntityId());
        if (!$holidaysData) {
            $this->insertHolidays($connection, $entity);
        } else {
            $this->updateHolidays($connection, $entity);
        }

        return $entity;
    }

    /**
     * insert details
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function insertDetails($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_details');
        $data = $entity->getStoresDetails();
        if ($data) {
            $data['store_id'] = (int)$entity->getEntityId();
            $connection->beginTransaction();
            try {
                $connection->insertMultiple($table, $data);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * update details
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function updateDetails($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_details');
        $data = $entity->getStoresDetails();
        if ($data) {
            $where = ['store_id = '.(int)$entity->getEntityId()];
            $connection->beginTransaction();
            try {
                $connection->update($table, $data, $where);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * insert timings
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function insertTimings($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_timings');
        $data = $entity->getStoresTimings();

        if ($data) {
            $data['store_id'] = (int)$entity->getEntityId();
            if (!empty($data['timings'])) {
                $data['timings'] = $this->dataHelper->serialize($data['timings']);
            }

            $connection->beginTransaction();
            try {
                $connection->insertMultiple($table, $data);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * update details
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function updateTimings($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_timings');
        $data = $entity->getStoresTimings();
        if ($data) {
            if (!empty($data['timings'])) {
                $data['timings'] = $this->dataHelper->serialize($data['timings']);
            }
            $where = ['store_id = '.(int)$entity->getEntityId()];
            $connection->beginTransaction();
            try {
                $connection->update($table, $data, $where);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * insert holidays
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function insertHolidays($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_stores_holidays_relation');
        $data = $entity->getStoresHolidays()['holidays'];
        $temp = [];
        if ($data) {
            foreach ($data as $holidayId) {
                $temp[] = [
                    'store_id' => (int)$entity->getEntityId(),
                    'holiday_id' => $holidayId
                ];
            }

            $data = $temp;
            $connection->beginTransaction();
            try {
                $connection->insertMultiple($table, $data);
                $connection->commit();
            } catch (\Exception $ex) {
                $connection->rollBack();
            }
        }
    }

    /**
     * update holidays
     * @param object $connection
     * @param object $entity
     * @return void
     */
    private function updateHolidays($connection, $entity)
    {
        $table = $this->resource->getTableName('webkul_storepickup_stores_holidays_relation');
        $data = $entity->getStoresHolidays();

        $this->deleteUnassignedHolidays(
            $connection,
            $data['holidays'],
            $entity->getEntityId()
        );

        $assignedHolidaysInDB = $this->getAssignedHolidays($connection, $entity->getEntityId());
        $assignedHolidayIds = array_column($assignedHolidaysInDB, 'holiday_id');

        if (!empty($data['holidays'])) {
            $newHolidayIds = [];
            foreach ($data['holidays'] as $holiday) {
                if (!in_array($holiday, $assignedHolidayIds)) {
                    $newHolidayIds[] = $holiday;
                }
            }

            if (!empty($newHolidayIds)) {
                $temp['holidays'] = $newHolidayIds;
                $entity->setStoresHolidays($temp);
                $this->insertHolidays($connection, $entity);
            }
        }
    }

    /**
     * get assigned holidays
     * @param object $connection
     * @param int $storeId
     * @return array
     */
    private function getAssignedHolidays($connection, $storeId)
    {
        $select = $connection->select()
            ->from(
                ['relation' => $this->resource->getTableName('webkul_storepickup_stores_holidays_relation')],
                ['holiday_id']
            )
            ->where('store_id = ?', $storeId);

        $connection->beginTransaction();
        try {
            $result = $connection->fetchAll($select);
            $connection->commit();
            return $result;
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * delete unassigned holidays
     * @param object $connection
     * @param array $holidayIds
     * @param int $storeId
     * @return void
     */
    private function deleteUnassignedHolidays($connection, $holidayIds, $storeId)
    {
        if (empty($holidayIds)) {
            $holidayIds = [0];
        }

        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->resource->getTableName('webkul_storepickup_stores_holidays_relation'),
                [
                    'store_id = ?' => $storeId,
                    'holiday_id not in (?)' => $holidayIds
                ]
            );
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }
}
