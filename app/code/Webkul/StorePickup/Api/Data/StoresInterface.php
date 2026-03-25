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

namespace Webkul\StorePickup\Api\Data;

interface StoresInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Table Fields
     */
    const ENTITY_ID = 'entity_id';
    const DESCRIPTION = 'description';
    const NAME = 'name';
    const IS_ENABLED = 'is_enabled';
    const LATITUDE = 'latitude';
    const CREATED_AT = 'created_at';
    const LONGITUDE = 'longitude';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setEntityId($entityId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setName($name);

    /**
     * Get is_enabled
     * @return string|null
     */
    public function getIsEnabled();

    /**
     * Set is_enabled
     * @param string $isEnabled
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setIsEnabled($isEnabled);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setDescription($description);

    /**
     * Get latitude
     * @return string|null
     */
    public function getLatitude();

    /**
     * Set latitude
     * @param string $latitude
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setLatitude($latitude);

    /**
     * Get longitude
     * @return string|null
     */
    public function getLongitude();

    /**
     * Set longitude
     * @param string $longitude
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setLongitude($longitude);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Webkul\StorePickup\Api\Data\StoresInterface
     */
    public function setCreatedAt($createdAt);
}
