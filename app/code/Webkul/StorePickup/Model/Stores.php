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

class Stores extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Webkul\StorePickup\Api\Data\StoresInterface
{
    /**
     * cache tag of stores
     * @var CACHE_TAG
     */
    const CACHE_TAG = 'webkul_storepickup_stores';

    /**
     * @var $_cacheTag
     */
    protected $_cacheTag = 'webkul_storepickup_stores';

    /**
     * @var $_eventPrefix
     */
    protected $_eventPrefix = 'webkul_storepickup_stores';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(\Webkul\StorePickup\Model\ResourceModel\Stores::class);
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
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get is_enabled
     * @return string|null
     */
    public function getIsEnabled()
    {
        return $this->getData(self::IS_ENABLED);
    }

    /**
     * Set is_enabled
     * @param string $isEnabled
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->setData(self::IS_ENABLED, $isEnabled);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get latitude
     * @return string|null
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * Set latitude
     * @param string $latitude
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * Get longitude
     * @return string|null
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * Set longitude
     * @param string $longitude
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
