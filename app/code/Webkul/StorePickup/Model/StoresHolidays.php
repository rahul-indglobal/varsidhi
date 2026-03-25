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

namespace Webkul\StorePickup\Model;

class StoresHolidays extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
{
    /**
     * cache tag of stores
     * @var CACHE_TAG
     */
    const CACHE_TAG = 'webkul_storepickup_holidays';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'webkul_storepickup_holidays';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_holidays';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(\Webkul\StorePickup\Model\ResourceModel\StoresHolidays::class);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * Get entity_id
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get is_single_date
     * @return string|null
     */
    public function getIsSingleDate()
    {
        return $this->getData(self::IS_SINGLE_DATE);
    }

    /**
     * Set is_single_date
     * @param string $isSingleDate
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setIsSingleDate($isSingleDate)
    {
        return $this->setData(self::IS_SINGLE_DATE, $isSingleDate);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get date
     * @return string|null
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Set date
     * @param string $date
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }
}
