<?php
/**
 * Copyright © 2018 Ideo. All rights reserved.
 * @license GPLv3
 */

namespace Ideo\StoreLocator\Api\Data;

interface StoreInterface
{
    const NAME = 'name';
    const COUNTRY = 'country';
    const CREATION_TIME = 'created_at';
    const UPDATE_TIME = 'updated_at';
    const IS_ACTIVE = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get Country
     *
     * @return string|null;
     */
    public function getCountry();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setId($id);

    /**
     * Set Name
     *
     * @param string $name
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setName($name);

    /**
     * Set country
     *
     * @param string $country
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setCountry($country);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Ideo\StoreLocator\Api\Data\StoreInterface
     */
    public function setIsActive($isActive);
}
