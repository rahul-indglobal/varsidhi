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

interface StoresHolidaysInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Table Fields
     */
    const ENTITY_ID = 'entity_id';
    const IS_SINGLE_DATE = 'is_single_date';
    const NAME = 'name';
    const DATE = 'date';
    const STATUS = 'status';

    /**
     * Get entity id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity id
     * @param string $entityId
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setEntityId($entityId);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setStatus($status);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Webkul\StorePickup\Api\Data\StoresHolidaysExtensionInterface $extensionAttributes
    );

    /**
     * Get is_single_date
     * @return string|null
     */
    public function getIsSingleDate();

    /**
     * Set is_single_date
     * @param string $isSingleDate
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setIsSingleDate($isSingleDate);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setName($name);

    /**
     * Get date
     * @return string|null
     */
    public function getDate();

    /**
     * Set date
     * @param string $date
     * @return \Webkul\StorePickup\Api\Data\StoresHolidaysInterface
     */
    public function setDate($date);
}
